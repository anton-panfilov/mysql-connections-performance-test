package httpclient

import (
	"net/http"
	"sync"
	"time"
)

type URL struct {
	Url  string
	Tags map[string]string
}

// RunTest performs the HTTP requests and returns results.
func RunTest(urls []URL, totalRequests, numThreads int) []HttpTestResult {
	requestsPerThread := totalRequests / numThreads
	var wg sync.WaitGroup
	client := &http.Client{}
	var mu sync.Mutex

	var results []HttpTestResult

	for _, url := range urls {
		start := time.Now()
		successCount := 0
		errorsCount := 0

		for i := 0; i < numThreads; i++ {
			wg.Add(1)
			go func() {
				defer wg.Done()

				for j := 0; j < requestsPerThread; j++ {
					resp, err := client.Get(url.Url)
					if err != nil || resp.StatusCode < 200 || resp.StatusCode > 299 {
						mu.Lock()
						errorsCount++
						mu.Unlock()
					} else {
						mu.Lock()
						successCount++
						mu.Unlock()
					}
					if resp != nil {
						resp.Body.Close()
					}
				}
			}()
		}

		wg.Wait()
		duration := time.Since(start).Seconds()

		results = append(results, HttpTestResult{
			Url:     url.Url,
			Tags:    url.Tags,
			Runtime: duration,
			Error:   errorsCount,
			Success: successCount,
		})
	}

	return results
}
