use axum::extract::Extension;
use mysql::prelude::*;
use mysql::*;
use std::sync::Arc;
use std::time::Instant;

pub async fn axum_endpoint(Extension(pool): Extension<Arc<Pool>>) -> String {
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
    )
    .unwrap();

    format!("Runtime: {:.9}", start.elapsed().as_secs_f64())
}