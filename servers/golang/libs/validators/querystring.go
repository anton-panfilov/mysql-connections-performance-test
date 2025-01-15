package validators

import (
	"github.com/go-playground/validator/v10"
	"net/url"
)

func ValidateQueryString(fl validator.FieldLevel) bool {
	_, err := url.ParseQuery(fl.Field().String())
	return err == nil
}
