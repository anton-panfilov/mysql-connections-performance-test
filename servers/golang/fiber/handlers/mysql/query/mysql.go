package query

import (
	"fmt"
	"github.com/gofiber/fiber/v2"
	"gotest/fiber/utils"
	"gotest/libs/schema"
	"log"
	"time"
)

func MysqlDriver(c *fiber.Ctx) error {
	first_element := uint32(1)
	data_size := utils.GetDataSizeForSelectsFromQuery(c)
	start := time.Now()
	for i := uint32(0); i < data_size; i++ {
		rows, err := db.Query("SELECT * FROM _sandbox where id = ?", i+first_element)
		if err == nil {
			for rows.Next() {
				var el schema.Sandbox
				if err := rows.Scan(
					&el.ID, &el.GUID, &el.Created, &el.Enum, &el.Int,
					&el.String, &el.Bool, &el.JSON, &el.EncryptedJSON,
				); err != nil {
					fmt.Println("Error scanning row:", err)
					return c.Status(500).SendString("Row scan failed: " + err.Error())
				}
			}
		}
		rows.Close()
		if err != nil {
			duration := time.Since(start).Seconds()
			log.Printf("Error End: %.05f seconds\n", duration)
			log.Printf("error: %s", err)
		}
	}
	duration := time.Since(start).Seconds()

	return c.Status(fiber.StatusCreated).JSON(schema.ResponseSelect{
		Language:  schema.Language,
		Test:      schema.TestSelect,
		Driver:    "mysql",
		Method:    "Query",
		Threads:   1,
		BatchSize: 1,
		Columns:   9,
		DataSize:  data_size,
		Duration:  duration,
	})
}
