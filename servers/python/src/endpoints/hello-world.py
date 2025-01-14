import httpx
from fastapi import APIRouter
from starlette.responses import PlainTextResponse

router = APIRouter()


@router.get("/hello-world")
async def run():
    return PlainTextResponse(f"hello world")
