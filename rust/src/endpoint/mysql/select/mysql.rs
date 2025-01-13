use mysql::prelude::*;
use mysql::*;
use std::sync::Arc;
use std::time::Instant;

use axum::{extract::Extension, Json};

use crate::scheme::response_select::ResponseSelect;

pub async fn axum_endpoint(Extension(pool): Extension<Arc<Pool>>) -> Json<ResponseSelect> {
    let columns: &str = "id";
    let first_id = 1;
    let iterations = 20000;

    let mut conn = pool.get_conn().unwrap();

    let start = Instant::now();
    for i in first_id..first_id + iterations {
        //let _: Option<(u64, Vec<u8>, String, i8, i32, String, bool, serde_json::Value, Vec<u8>)> =
        let _: Option<u64> = conn
            .exec_first(format!(r"select {columns} from _sandbox where id={i}"), ())
            .unwrap();
    }
    let duration = start.elapsed().as_secs_f64();

    Json(ResponseSelect {
        driver: "mysql",
        method: "query_drop",
        data_size: iterations,
        columns: 1,
        duration,
        ..ResponseSelect::default()
    })
}
