import httpx
from fastapi import APIRouter
from starlette.responses import PlainTextResponse

router = APIRouter()


@router.get("/hello-world")
async def run():
    # Make an async HTTP request using httpx
    async with httpx.AsyncClient() as client:
        response = await client.get("https://leaptheory.com/api/test/a1")
        # Combine "hello world" with the response content
        return PlainTextResponse(f"hello world: {response.text}")
