package main

import (
	"fmt"
	_ "github.com/go-sql-driver/mysql"
	"github.com/gofiber/fiber/v2"
	"gotest/libs/schema"
	"gotest/libs/utils"
	"io"
	"log"
	"net/http"
	"net/url"
	"strconv"
	"time"
)

func url_get_contents(url string) ([]byte, error) {
	// Create a new HTTP request
	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		return nil, err
	}

	// Set a custom User-Agent header
	req.Header.Set("User-Agent", "APTest/1.0")

	// Use the default HTTP client to execute the request
	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()

	// Read the response body
	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, err
	}

	return body, nil
}

func getDataSizeFromQuery(c *fiber.Ctx) uint32 {
	dataSizeParam := c.Query("s", "")
	if dataSizeParam != "" {
		parsedDataSize, err := strconv.Atoi(dataSizeParam)
		if err == nil && parsedDataSize >= 1 && parsedDataSize <= 100000 {
			return uint32(parsedDataSize)
		}
	}
	return 20000
}

func main() {
	//environment := utils.GetEnvironmentInstance()
	//db, err := sql.Open("mysql", environment.Percona().DataSource())
	//if err != nil {
	//	log.Fatalf("Failed to connect to the database: %v", err)
	//}
	//defer db.Close()
	//
	//// Set connection pool settings
	//db.SetMaxOpenConns(20)                  // Limit the number of open connections
	//db.SetMaxIdleConns(10)                  // Number of idle connections to keep
	//db.SetConnMaxLifetime(30 * time.Second) // Lifetime of each connection
	//
	//gormDB, err := gorm.Open(mysql.New(mysql.Config{
	//	Conn: db,
	//}), &gorm.Config{
	//	//Logger: logger.Default.LogMode(logger.Info),
	//})

	err := utils.InitPercona()
	if err != nil {
		panic(err)
	}

	app := fiber.New(fiber.Config{
		AppName:       "PL.api",
		CaseSensitive: true,
	})

	app.Static("/favicon.ico", "./img/favicon.svg")

	app.Get("/hello-world", func(c *fiber.Ctx) error {
		c.WriteString("hello world")
		return nil
	})

	app.Get("/http-client", func(c *fiber.Ctx) error {
		link := c.Query("link", "")
		if link == "" {
			return c.Status(http.StatusBadRequest).SendString("Query parameter 'link' is required")
		}

		_, err := url.ParseRequestURI(link)
		if err != nil {
			return c.Status(http.StatusBadRequest).SendString("Invalid 'link' URL")
		}

		body, err := url_get_contents(link)
		if err != nil {
			return err
		}
		c.WriteString(string(body))
		return nil
	})

	app.Get("/select/serial/mysql", func(c *fiber.Ctx) error {
		first_element := uint32(1)
		data_size := getDataSizeFromQuery(c)
		start := time.Now()
		for i := uint32(0); i < data_size; i++ {
			rows, err := utils.PerconaConnection.Query("SELECT * FROM _sandbox where id = ?", i+first_element)
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
		data_size := getDataSizeFromQuery(c)
		start := time.Now()
		stmt, _ := utils.PerconaConnection.Prepare("SELECT * FROM _sandbox where id = ?")
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
		data_size := getDataSizeFromQuery(c)
		var result schema.Sandbox

		start := time.Now()
		for i := uint32(0); i < data_size; i++ {
			utils.PerconaConnectionGorm.Raw("SELECT * FROM _sandbox where id=?", i).Scan(&result)

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

	err = app.Listen(fmt.Sprintf(":%d", utils.GetEnvironmentInstance().ServerPort))
	if err != nil {
		log.Fatal(err)
	}
}
