package main

import (
	"fmt"
	"pt/lib/httpclient"
	"pt/lib/tests"
)

func main() {
	waitSec := 300000
	requests := 28
	threads := 28

	fmt.Printf("Hello sleep test, requests: %d, threads: %d, wait: %d sec:\n", requests, threads, waitSec)
	httpclient.RenderTable(
		tests.HelloSleepTest(requests, threads, waitSec),
	)
}
