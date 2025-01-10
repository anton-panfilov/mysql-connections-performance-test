use serde::Serialize;

#[derive(Serialize)]
pub struct ResponseSelect {
    pub language: &'static str,
    pub test: &'static str,
    pub driver: &'static str,
    pub method: &'static str,
    pub threads: u8,
    pub batch_size: u32,
    pub data_size: u32,
    pub columns: u8,
    pub duration: f64,
}

impl ResponseSelect {
    pub(crate) fn default() -> ResponseSelect {
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