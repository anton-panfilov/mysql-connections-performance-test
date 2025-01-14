package httpclient

import (
	"fmt"
	"strings"
)

func RenderTable(results []HttpTestResult) {
	// Base headers
	headers := []string{"URL", "Runtime", "Success", "Errors"}

	// Collect all unique tag keys
	tagKeys := make(map[string]struct{})
	for _, result := range results {
		for tag := range result.Tags {
			tagKeys[tag] = struct{}{}
		}
	}

	// If there are tags, append them to the headers
	var sortedTagKeys []string
	for tag := range tagKeys {
		sortedTagKeys = append(sortedTagKeys, tag)
	}
	headers = append(headers, sortedTagKeys...)

	// Calculate column widths
	columnWidths := make([]int, len(headers))
	for i, header := range headers {
		columnWidths[i] = len(header)
	}

	for _, result := range results {
		columnWidths[0] = max(columnWidths[0], len(result.Url))
		columnWidths[1] = max(columnWidths[1], len(fmt.Sprintf("%.2f", result.Runtime)))
		columnWidths[2] = max(columnWidths[2], len(fmt.Sprintf("%d", result.Success)))
		columnWidths[3] = max(columnWidths[3], len(fmt.Sprintf("%d", result.Error)))

		for i, tag := range sortedTagKeys {
			columnWidths[4+i] = max(columnWidths[4+i], len(result.Tags[tag]))
		}
	}

	// Print the table
	printSeparator(columnWidths)
	printRow(headers, columnWidths)
	printSeparator(columnWidths)

	for _, result := range results {
		row := []string{
			result.Url,
			fmt.Sprintf("%.2f", result.Runtime),
			fmt.Sprintf("%d", result.Success),
			fmt.Sprintf("%d", result.Error),
		}

		for _, tag := range sortedTagKeys {
			row = append(row, result.Tags[tag]) // Add tag value or blank if missing
		}
		printRow(row, columnWidths)
	}
	printSeparator(columnWidths)
}

func max(a, b int) int {
	if a > b {
		return a
	}
	return b
}

func printRow(row []string, widths []int) {
	for i, cell := range row {
		fmt.Printf("%-*s ", widths[i], cell)
	}
	fmt.Println()
}

func printSeparator(widths []int) {
	var separator []string
	for _, width := range widths {
		separator = append(separator, strings.Repeat("-", width))
	}
	fmt.Println(strings.Join(separator, " "))
}
