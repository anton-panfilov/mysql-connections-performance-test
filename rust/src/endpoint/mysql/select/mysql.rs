use mysql::prelude::*;
use mysql::*;
use std::sync::Arc;
use std::time::Instant;

use axum::{extract::Extension, Json};
use crate::scheme::response_select::ResponseSelect;

pub async fn axum_endpoint(Extension(pool): Extension<Arc<Pool>>) -> Json<ResponseSelect> {
    let columns: &str = "*";
    let first_id = 1;
    let iterations = 20000;

    let mut conn = pool.get_conn().unwrap();

    let start = Instant::now();
    for i in first_id..first_id + iterations {
        let _ = conn.query_drop(format!(r"select {columns} from _sandbox where id={i}"));
    }
    let duration = start.elapsed().as_secs_f64();

    Json(ResponseSelect {
        driver: "mysql",
        method: "query_drop",
        data_size: iterations,
        duration,
        ..ResponseSelect::default()
    })
}
