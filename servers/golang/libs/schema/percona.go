package schema

import (
	"fmt"
	"github.com/go-playground/validator/v10"
)

const DefaultMySQLPort uint = 3306

type Percona struct {
	Host string `validate:"required,hostname"`
	Port uint   `validate:"port"`
	User string `validate:"required"`
	Pass string `validate:"required"`
	Base string `validate:"required"`

	// https://github.com/go-sql-driver/mysql?tab=readme-ov-file#parameters
	Parameters string
}

func ApplyDefaults(config Percona) Percona {
	if config.Port == 0 {
		config.Port = DefaultMySQLPort
	}
	return config
}

func (p Percona) Validate() error {
	return validator.New().Struct(p)
}

func (p Percona) ValidatePanic() {
	err := p.Validate()
	if err != nil {
		panic(err)
	}
}

func (p Percona) DataSource() string {
	return fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?%s",
		p.User, p.Pass, p.Host, p.Port, p.Base, p.Parameters)
}
