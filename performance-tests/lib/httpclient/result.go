package httpclient

type HttpTestResult struct {
	Url     string
	Tags    map[string]string
	Runtime float64
	Success int
	Error   int
}
