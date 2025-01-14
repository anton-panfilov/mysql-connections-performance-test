from fastapi import APIRouter, Query
from sqlalchemy import create_engine
from time import perf_counter
from sqlalchemy.sql import text

from helpers.db import make_connection_url_from_env
from scheme.select_test_result import ResponseSelect

router = APIRouter()

@router.get("/select/serial/sqlalchemy+mysql+mysqldb+execute")
async def run(s: int = Query(default=20000, ge=1, le=100000)):
    data_size: int = s
    first_id: int = 1
    columns = "*"

    perf_counter()
    engine = create_engine(make_connection_url_from_env("mysql+mysqldb"))
    with engine.begin() as conn:
        start_time = perf_counter()
        for i in range(first_id, first_id + data_size):
            query = text(f"SELECT {columns} FROM _sandbox WHERE id = :id")
            conn.execute(query, {"id": i})
        duration = perf_counter() - start_time

    return ResponseSelect(
        test="select",
        driver="sqlalchemy+mysqldb",
        method="execute",
        data_size=data_size,
        duration=duration,
        columns=8
    )