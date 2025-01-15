package tests

import "pt/lib/httpclient"
import "net/url"

func HttpClientTest(totalRequests int, numThreads int, link string) []httpclient.HttpTestResult {
	urls := []httpclient.URL{
		{
			Url: "http://python.pt/http-client?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang":      "python",
				"framework": "fastapi",
				"lib":       "httpx",
			},
		},
		{
			Url: "http://php.pt/http-client/file_get_contents?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang": "php",
				"lib":  "file_get_contents",
			},
		},
		{
			Url: "http://php.pt/http-client/curl?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang": "php",
				"lib":  "curl",
			},
		},
		{
			Url: "http://go.pt/http-client?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang":      "go",
				"framework": "fiber",
				"lib":       "net/http",
			},
		},
		{
			Url: "http://nest.pt/http-client?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang":      "node",
				"framework": "nest",
				"lib":       "fetch",
			},
		},
		{
			Url: "http://fastify.nest.pt/http-client?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang":      "node",
				"framework": "nest[fastify]",
				"lib":       "fetch",
			},
		},
		{
			Url: "http://express.pt/http-client?link=" + url.QueryEscape(link),
			Tags: map[string]string{
				"lang":      "node",
				"framework": "express",
				"lib":       "fetch",
			},
		},
	}
	return httpclient.RunTest(urls, totalRequests, numThreads)
}
