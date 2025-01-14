package env

import (
	"fmt"
	"github.com/caarlos0/env/v11"
	"github.com/go-playground/validator/v10"
	"reflect"
)

func LoadEnvironment() (*Environment, error) {
	var environment Environment

	environment, err := env.ParseAsWithOptions[Environment](env.Options{})
	if err := env.Parse(&environment); err != nil {
		return nil, err
	}

	err = validator.New().Struct(environment)
	if err != nil {
		errorMessages := ""
		typ := reflect.TypeOf(environment)
		for _, err := range err.(validator.ValidationErrors) {
			field, _ := typ.FieldByName(err.Field())
			envName := field.Tag.Get("env")
			errorMessages += fmt.Sprintf(
				"Invalid '%s' (%v), validator: %s %s\n",
				envName,
				err.Value(),
				err.ActualTag(),
				err.Param(),
			)
		}
		return nil, fmt.Errorf(errorMessages)
	}

	return &environment, nil
}

func LoadCorrectEnvironmentOrPanic() *Environment {
	environment, err := LoadEnvironment()
	if err != nil {
		panic(fmt.Sprintf("Environment loading failed:\n%s", err))
	}
	return environment
}
