use mysql::prelude::*;
use mysql::*;
use std::sync::Arc;
use std::time::Instant;

use axum::{extract::Extension, routing::get, Json, Router};
use chrono::{DateTime, Utc};
use serde::Serialize;
use serde_json::Value;
use sqlx::mysql::MySqlPoolOptions;
use sqlx::MySql;

#[tokio::main]
async fn main() {
    tracing_subscriber::fmt::init();
    let url = std::env::var("DATABASE_URL").unwrap();

    let mysql_pool = Pool::new(url.as_str()).unwrap();
    let mysql_pool_shared = Arc::new(mysql_pool);

    let sqlx_pool: sqlx::Pool<MySql> = MySqlPoolOptions::new()
        .max_connections(5)
        .connect(url.as_str()).await.unwrap();
    let sqlx_pool_shared = Arc::new(sqlx_pool);

    let app = Router::new()
        .route("/", get(root))
        .route("/migration", get(migration))
        .route("/mysql_serial_select_20k", get(mysql_serial_select_20k))
        .route("/sqlx_serial_select_20k", get(sqlx_serial_select_20k))
        .layer(Extension(mysql_pool_shared))
        .layer(Extension(sqlx_pool_shared));

    let bind = std::env::var("APP_BIND").unwrap();
    let listener = tokio::net::TcpListener::bind(bind).await.unwrap();
    axum::serve(listener, app)
        .await.unwrap();
}

#[derive(Serialize)]
struct ResponseSelect {
    language: &'static str,
    test: &'static str,
    driver: &'static str,
    method: &'static str,
    threads: u8,
    batch_size: u32,
    data_size: u32,
    columns: u8,
    duration: f64,
}

impl ResponseSelect {
    fn default() -> ResponseSelect {
        ResponseSelect {
            language: default_language(),
            test: "select",
            driver: "",
            method: "",
            threads: 1,
            batch_size: 1,
            data_size: 0,
            columns: 9,
            duration: 0.0,
        }
    }
}

fn default_language() -> &'static str { "rust" }

// basic handler that responds with a static string
async fn root() -> &'static str {
    "Hello, World 41!"
}

async fn migration(
    Extension(pool): Extension<Arc<Pool>>
) -> String {
    let mut conn = pool.get_conn().unwrap();

    let start = Instant::now();

    conn.query_drop(
        r"CREATE TABLE IF NOT EXISTS `_sandbox` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `guid` binary(16) NOT NULL,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `enum` tinyint(4) NOT NULL DEFAULT '1',
              `int` int(11) NOT NULL,
              `string` varchar(200) NOT NULL,
              `bool` tinyint(1) NOT NULL DEFAULT '0',
              `json` json NOT NULL,
              `encrypted_json` longblob NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    ).unwrap();

    format!("Runtime: {:.9}", start.elapsed().as_secs_f64())
}


async fn mysql_serial_select_20k(
    Extension(pool): Extension<Arc<Pool>>
) -> Json<ResponseSelect> {
    let columns: &str = "*";
    let first_id = 1;
    let iterations = 20000;

    let mut conn = pool.get_conn().unwrap();

    let start = Instant::now();
    for i in first_id..first_id + iterations {
        let _ = conn.query_drop(format!(r"select {columns} from _sandbox where id={i}"));
    }
    let duration = start.elapsed().as_secs_f64();

    Json(
        ResponseSelect {
            driver: "mysql",
            method: "query_drop",
            data_size: iterations,
            duration,
            ..ResponseSelect::default()
        }
    )
}

async fn sqlx_serial_select_20k(
    Extension(pool): Extension<Arc<sqlx::Pool<MySql>>>
) -> Json<ResponseSelect> {
    let columns: &str = "`id`, `guid`, `created`, `enum`, `int`, `string`, `bool`, `json`, `encrypted_json`";
    let first_id = 1;
    let iterations = 20000;

    let start = Instant::now();
    for i in first_id..first_id + iterations {
        let _row: (u64, Vec<u8>, DateTime<Utc>, i8, i32, String, bool, Value, Vec<u8>) = sqlx::query_as(
            format!("SELECT {columns} FROM _sandbox where id=?").as_str()
        )
            .bind(i)
            .fetch_one(&*pool)
            .await
            .unwrap();

        // println!("{:#?}", _row);
    }
    let duration = start.elapsed().as_secs_f64();

    Json(
        ResponseSelect {
            driver: "sqlx.mysql",
            method: "query_as",
            data_size: iterations,
            duration,
            ..ResponseSelect::default()
        }
    )
}