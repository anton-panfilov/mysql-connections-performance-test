package tests

import (
	"fmt"
	"pt/lib/httpclient"
)

func HelloSleepTest(totalRequests int, numThreads int, waitS int) []httpclient.HttpTestResult {
	urls := []httpclient.URL{
		{
			Url:  fmt.Sprintf("http://nest.pt/hello-sleep-no-promise?sec=%d", waitS),
			Tags: map[string]string{"lang": "node", "framework": "nest"},
		},
	}
	return httpclient.RunTest(urls, totalRequests, numThreads)
}
