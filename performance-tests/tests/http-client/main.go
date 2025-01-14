package main

import (
	"fmt"
	"pt/lib/httpclient"
	"pt/lib/tests"
)

func main() {
	var link string

	requests := 50
	threads := 10

	link = "https://43money.com/"
	fmt.Printf("HTTP Client test %s, requests: %d, threads: %d:\n", link, requests, threads)
	httpclient.RenderTable(
		tests.HttpClientTest(requests, threads, link),
	)

	fmt.Println()
	fmt.Println()
	link = "https://leaptheory.com/api/test/a1"
	fmt.Printf("HTTP Client test %s, requests: %d, threads: %d:\n", link, requests, threads)
	httpclient.RenderTable(
		tests.HttpClientTest(requests, threads, link),
	)

	fmt.Println()
	fmt.Println()
	link = "https://api.leaptheory.com/name"
	fmt.Printf("HTTP Client test %s, requests: %d, threads: %d:\n", link, requests, threads)
	httpclient.RenderTable(
		tests.HttpClientTest(requests, threads, link),
	)

	fmt.Println()
	fmt.Println()
	link = "https://catfact.ninja/fact"
	fmt.Printf("HTTP Client test %s, requests: %d, threads: %d:\n", link, requests, threads)
	httpclient.RenderTable(
		tests.HttpClientTest(requests, threads, link),
	)
}
