package utils

import (
	"github.com/go-playground/validator/v10"
	"gotest/libs/helpers"
	"gotest/libs/schema"
	"gotest/libs/validators"
	"sync"
)

var (
	environmentInstance *schema.Environment
	once                sync.Once
)

func GetEnvironmentInstance() *schema.Environment {
	once.Do(func() {
		environmentInstance = &schema.Environment{}

		v := validator.New()
		err := v.RegisterValidation("querystring", validators.ValidateQueryString)
		if err != nil {
			panic("Error registering query validator: " + err.Error())
		}

		err = helpers.LoadEnvironmentWithCustomValidator(environmentInstance, v)
		if err != nil {
			panic("Environment loading failed: " + err.Error())
		}
	})
	return environmentInstance
}
