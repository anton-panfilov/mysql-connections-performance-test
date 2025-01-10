import os
from time import perf_counter

import MySQLdb
from MySQLdb._mysql import connection
from MySQLdb.cursors import Cursor
from fastapi import APIRouter

from scheme.select_test_result import ResponseSelect

router = APIRouter()


@router.get("/select/serial/mysqldb+query")
async def run():
    data_size: int = 20000
    first_id: int = 1
    columns = "*"
    conn: connection = MySQLdb.connect(
        host=os.environ['DB_HOST'],
        port=int(os.environ['DB_PORT']),
        user=os.environ['DB_USER'],
        passwd=os.environ['DB_PASS'],
        db=os.environ['DB_BASE']
    )

    start_time = perf_counter()
    for i in range(first_id, first_id + data_size):
        conn.query(f"SELECT {columns} FROM _sandbox WHERE id = 1")
        res = conn.store_result().fetch_row(maxrows=0, how=1)
    duration = perf_counter() - start_time
    conn.close()

    return ResponseSelect(
        test="select",
        driver="mysqldb",
        method="execute",
        data_size=data_size,
        duration=duration,
        columns=8,
    )
