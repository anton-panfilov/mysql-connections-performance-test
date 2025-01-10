package main

import (
	"api/config/env"
	"api/schema"
	"database/sql"
	"fmt"
	_ "fmt"
	_ "github.com/go-sql-driver/mysql"
	"github.com/gofiber/fiber/v2"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"log"
	"time"
)

type Product struct {
	gorm.Model
	Code  string
	Price uint
}

const TestSelectDataSize uint32 = 20000

func main() {
	environment := env.LoadCorrectEnvironmentOrPanic()
	db, err := sql.Open("mysql", environment.PerconaMain().DataSource())
	if err != nil {
		log.Fatalf("Failed to connect to the database: %v", err)
	}
	defer db.Close()

	// Set connection pool settings
	db.SetMaxOpenConns(20)                  // Limit the number of open connections
	db.SetMaxIdleConns(10)                  // Number of idle connections to keep
	db.SetConnMaxLifetime(30 * time.Second) // Lifetime of each connection

	gormDB, err := gorm.Open(mysql.New(mysql.Config{
		Conn: db,
	}), &gorm.Config{
		//Logger: logger.Default.LogMode(logger.Info),
	})

	app := fiber.New(fiber.Config{
		AppName:       "PL.api",
		CaseSensitive: true,
	})

	app.Static("/favicon.ico", "./img/favicon.svg")

	app.Get("/select/serial/mysql", func(c *fiber.Ctx) error {
		first_element := uint32(1)
		data_size := TestSelectDataSize
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
	})

	app.Get("/select/serial/mysql+one(prepare)", func(c *fiber.Ctx) error {
		first_element := uint32(1)
		data_size := TestSelectDataSize
		start := time.Now()
		stmt, _ := db.Prepare("SELECT * FROM _sandbox where id = ?")
		for i := uint32(0); i < data_size; i++ {
			var el schema.Sandbox
			row := stmt.QueryRow(i + first_element)
			if err := row.Scan(
				&el.ID, &el.GUID, &el.Created, &el.Enum, &el.Int,
				&el.String, &el.Bool, &el.JSON, &el.EncryptedJSON,
			); err != nil {
				fmt.Println("Error scanning row:", err)
				return c.Status(500).SendString("Row scan failed: " + err.Error())
			}
			//row.Close()
		}
		duration := time.Since(start).Seconds()
		log.Printf("SQL end: %.05f seconds\n", duration)

		return c.Status(fiber.StatusCreated).JSON(schema.ResponseSelect{
			Language:  schema.Language,
			Test:      schema.TestSelect,
			Driver:    "mysql",
			Method:    "one(Prepare)+QueryRow",
			Threads:   1,
			BatchSize: 1,
			Columns:   9,
			DataSize:  data_size,
			Duration:  duration,
		})
	})

	app.Get("/select/serial/gorm+raw", func(c *fiber.Ctx) error {
		data_size := TestSelectDataSize
		var result schema.Sandbox

		start := time.Now()
		for i := uint32(0); i < data_size; i++ {
			gormDB.Raw("SELECT * FROM _sandbox limit 1").Scan(&result)

			if err != nil {
				duration := time.Since(start).Seconds()
				log.Printf("Error End: %.05f seconds\n", duration)
				log.Printf("error: %s", err)
			}
		}
		duration := time.Since(start).Seconds()
		log.Printf("End: %.05f seconds\n", duration)

		return c.Status(fiber.StatusCreated).JSON(schema.ResponseSelect{
			Language:  schema.Language,
			Test:      schema.TestSelect,
			Driver:    "gorm",
			Method:    "Raw",
			Threads:   1,
			BatchSize: 1,
			Columns:   9,
			DataSize:  data_size,
			Duration:  duration,
		})
	})

	err = app.Listen(fmt.Sprintf(":%d", environment.ServerPort))
	if err != nil {
		log.Fatal(err)
	}
}
