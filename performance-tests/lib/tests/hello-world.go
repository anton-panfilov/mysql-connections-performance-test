package tests

import "pt/lib/httpclient"

func HelloWorldTest(totalRequests int, numThreads int) []httpclient.HttpTestResult {
	urls := []httpclient.URL{
		{Url: "http://python.pt/hello-world", Tags: map[string]string{"lang": "python"}},
		{Url: "http://php.pt/hello-world", Tags: map[string]string{"lang": "php"}},
		{Url: "http://go.pt/hello-world", Tags: map[string]string{"lang": "go"}},
	}
	return httpclient.RunTest(urls, totalRequests, numThreads)
}
