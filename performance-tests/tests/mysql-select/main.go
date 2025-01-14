package main

import (
	"fmt"
	"pt/lib/httpclient"
	"pt/lib/tests"
)

func main() {
	fmt.Printf("50 requests, 10 threads, connection(maybe from pool) + 100 queries on each client:\n")

	httpclient.RenderTable(
		tests.MysqlSelectsTest(100, 10, 100),
	)

	fmt.Println()
	fmt.Println()
	fmt.Printf("200 requests, 10 threads, connection(maybe from pool) + 2 queries on each client:\n")
	httpclient.RenderTable(
		tests.MysqlSelectsTest(500, 10, 1),
	)
}
