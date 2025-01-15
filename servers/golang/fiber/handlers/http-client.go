package handlers

import "github.com/gofiber/fiber/v2"

func HttpClient(c *fiber.Ctx) error {
	return c.SendString("hello world")
}
