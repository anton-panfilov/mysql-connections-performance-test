import importlib


def include_routers_from_dir(app, base_path, base_module):
    """
    Recursively scan directories and include routers from Python files.
    :param app: FastAPI application instance
    :param base_path: Base file path to scan
    :param base_module: Base module path (dot-separated)
    """

    for item in base_path.iterdir():
        if item.is_dir():
            include_routers_from_dir(app, item, f"{base_module}.{item.name}")
        elif item.suffix == ".py" and item.name != "__init__.py":
            module_name = f"{base_module}.{item.stem}"
            module = importlib.import_module(module_name)
            if hasattr(module, "router"):
                app.include_router(module.router)