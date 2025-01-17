package main

import (
	"fmt"
	"pt/lib/httpclient"
	"pt/lib/tests"
)

func main() {
	requests := 50
	threads := 10

	links := []string{
		"https://43money.com/",
		"https://leaptheory.com/api/test/a1",
		"https://api.leaptheory.com/name",
		"https://api.coindesk.com/v1/bpi/currentprice.json",
	}

	var link string
	for _, link = range links {
		fmt.Printf("HTTP Client test %s, requests: %d, threads: %d:\n", link, requests, threads)
		httpclient.RenderTable(
			tests.HttpClientTest(requests, threads, link),
		)
		fmt.Printf("\n\n")
	}
}
