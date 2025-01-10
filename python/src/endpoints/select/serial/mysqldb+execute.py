import os
from time import perf_counter

import MySQLdb
from fastapi import APIRouter

from scheme.select_test_result import ResponseSelect

router = APIRouter()


@router.get("/select/serial/mysqldb+execute")
async def run():
    data_size: int = 20000
    first_id: int = 1
    columns = "*"
    conn = MySQLdb.connect(
        host=os.environ['DB_HOST'],
        port=int(os.environ['DB_PORT']),
        user=os.environ['DB_USER'],
        passwd=os.environ['DB_PASS'],
        db=os.environ['DB_BASE']
    )
    cursor = conn.cursor()
    start_time = perf_counter()
    for i in range(first_id, first_id + data_size):
        cursor.execute(f"SELECT {columns} FROM _sandbox WHERE id = 1")
        rows = cursor.fetchall()
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
