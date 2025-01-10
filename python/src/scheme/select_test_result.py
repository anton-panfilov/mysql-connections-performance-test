class ResponseSelect:
    def __init__(
            self,
            language: str = "python",
            test: str = "mysql",
            driver: str = "mysql",
            method: str = "query_drop",
            threads: int = 1,
            batch_size: int = 1,
            data_size: int = 20000,
            columns: int = 8,
            duration: float = 0.0
    ):
        self.language = language
        self.test = test
        self.driver = driver
        self.method = method
        self.threads = threads
        self.batch_size = batch_size
        self.data_size = data_size
        self.columns = columns
        self.duration = duration
