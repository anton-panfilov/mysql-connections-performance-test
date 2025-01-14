package main

import (
	"fmt"
	"pt/lib/httpclient"
	"pt/lib/tests"
)

func main() {
	fmt.Printf("Hello world test:\n")
	httpclient.RenderTable(
		tests.HelloWorldTest(5000, 10),
	)
}
