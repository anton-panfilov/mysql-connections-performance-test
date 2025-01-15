package helpers

import (
	"fmt"
	"github.com/caarlos0/env/v11"
	"github.com/go-playground/validator/v10"
	"reflect"
)

func LoadEnvironmentWithCustomValidator[T any](environment *T, v *validator.Validate) error {
	var err error

	*environment, err = env.ParseAsWithOptions[T](env.Options{})
	if err := env.Parse(environment); err != nil {
		return err
	}

	err = v.Struct(environment)
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
		return fmt.Errorf(errorMessages)
	}

	return nil
}

func LoadEnvironment[T any](environment *T) error {
	return LoadEnvironmentWithCustomValidator(environment, validator.New())
}
