package routes

import (
	"github.com/gofiber/fiber/v2"
	"gotest/fiber/handlers"
)

func Routes(app *fiber.App) {
	app.Get("/", handlers.HelloWorld)
	app.Get("/hello-world", handlers.HelloWorld)
}
