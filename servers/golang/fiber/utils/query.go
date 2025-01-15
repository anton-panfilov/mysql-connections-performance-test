package utils

import (
	"github.com/gofiber/fiber/v2"
	"strconv"
)

func GetDataSizeForSelectsFromQuery(c *fiber.Ctx) uint32 {
	dataSizeParam := c.Query("s", "")
	if dataSizeParam != "" {
		parsedDataSize, err := strconv.Atoi(dataSizeParam)
		if err == nil && parsedDataSize >= 1 && parsedDataSize <= 100000 {
			return uint32(parsedDataSize)
		}
	}
	return 20000
}
