import importlib
import os


def make_connection_url(
        protocol: str,
        host: str,
        user: str,
        passwd: str,
        port: int,
        base: str,
) -> str:
    return f"{protocol}://{user}:{passwd}@{host}:{port}/{base}"


def make_connection_url_from_env(protocol: str) -> str:
    return make_connection_url(
        protocol=protocol,
        host=os.environ['DB_HOST'],
        user=os.environ['DB_USER'],
        passwd=os.environ['DB_PASS'],
        port=int(os.environ['DB_PORT']),
        base=os.environ['DB_BASE'],
    )