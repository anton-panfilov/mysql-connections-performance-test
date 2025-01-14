package tests

import (
	"fmt"
	"pt/lib/httpclient"
)

func MysqlSelectsTest(totalRequests int, numThreads int, queries int) []httpclient.HttpTestResult {
	urls := []httpclient.URL{
		{
			Url: fmt.Sprintf("http://python.pt/select/serial/mysqldb+query?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "python",
				"connection": "new",
				"driver":     "mysqldb",
				"method":     "query",
			},
		},
		{
			Url: fmt.Sprintf("http://php.pt/mysql/select/serial/query/mysqli?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "php",
				"connection": "new",
				"driver":     "mysqli",
				"method":     "query",
			},
		},
		{
			Url: fmt.Sprintf("http://php.pt/mysql/select/serial/query/mysqli_pers?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "php",
				"connection": "pool",
				"driver":     "mysqli",
				"method":     "query",
			},
		},
		{
			Url: fmt.Sprintf("http://go.pt/select/serial/mysql?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "go",
				"connection": "pool",
				"driver":     "mysql",
				"method":     "Query",
			},
		},
		{
			Url: fmt.Sprintf("http://go.pt/select/serial/gorm+raw?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "go",
				"connection": "pool",
				"driver":     "gorm",
				"method":     "raw",
			},
		},
		{
			Url: fmt.Sprintf("http://rust.pt/select/serial/mysql?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "rust",
				"connection": "pool",
				"driver":     "mysql",
				"method":     "exec_first",
			},
		},
		{
			Url: fmt.Sprintf("http://rust.pt/select/serial/sqlx?s=%d", queries),
			Tags: map[string]string{
				"queries":    fmt.Sprintf("%d", queries),
				"lang":       "rust",
				"connection": "pool",
				"driver":     "sqlx",
				"method":     "query_as",
			},
		},
	}
	return httpclient.RunTest(urls, totalRequests, numThreads)
}
