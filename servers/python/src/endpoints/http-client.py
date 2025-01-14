import httpx
from fastapi import APIRouter, Query
from starlette.responses import PlainTextResponse
from pydantic import HttpUrl

router = APIRouter()

@router.get("/http-client")
async def run(link: HttpUrl = Query()):
    async with httpx.AsyncClient() as client:
        response = await client.get(str(link))
        return PlainTextResponse(response.text)
