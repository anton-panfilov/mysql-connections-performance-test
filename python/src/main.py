import pathlib

from fastapi import FastAPI

from helpers.router import include_routers_from_dir

app = FastAPI()

include_routers_from_dir(
    app=app,
    base_path=pathlib.Path(__file__).parent / "endpoints",
    base_module="endpoints"
)
