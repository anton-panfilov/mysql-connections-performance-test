package tests

import "pt/lib/httpclient"

func HelloWorldTest(totalRequests int, numThreads int) []httpclient.HttpTestResult {
	urls := []httpclient.URL{
		{Url: "http://python.pt/hello-world", Tags: map[string]string{"lang": "python", "framework": "fastapi"}},
		{Url: "http://php.pt/hello-world", Tags: map[string]string{"lang": "php", "framework": ""}},
		{Url: "http://go.pt/hello-world", Tags: map[string]string{"lang": "go", "framework": "fiber"}},
		{Url: "http://rust.pt/hello-world", Tags: map[string]string{"lang": "rust", "framework": "axum"}},
		{Url: "http://nest.pt/hello-world", Tags: map[string]string{"lang": "node", "framework": "nest"}},
		{Url: "http://fastify.nest.pt/hello-world", Tags: map[string]string{"lang": "node", "framework": "nest[fastify]"}},
		{Url: "http://express.pt/hello-world", Tags: map[string]string{"lang": "node", "framework": "express"}},
		{Url: "http://no-any-server.pt/hello-world", Tags: map[string]string{}},
	}
	return httpclient.RunTest(urls, totalRequests, numThreads)
}
