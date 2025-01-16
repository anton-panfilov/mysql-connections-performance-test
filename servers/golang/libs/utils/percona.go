package utils

import (
	"database/sql"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"time"
)

var PerconaConnection *sql.DB
var PerconaConnectionGorm *gorm.DB

func InitPercona() error {
	var err error
	environment := GetEnvironmentInstance()

	PerconaConnection, err = sql.Open("mysql", environment.Percona().DataSource())
	if err != nil {
		return err
	}
	defer PerconaConnection.Close()

	PerconaConnection.SetMaxOpenConns(20)
	PerconaConnection.SetMaxIdleConns(10)
	PerconaConnection.SetConnMaxLifetime(30 * time.Second)

	PerconaConnectionGorm, err = gorm.Open(mysql.New(mysql.Config{
		Conn: PerconaConnection,
	}), &gorm.Config{
		// Logger: logger.Default.LogMode(logger.Info),
	})

	return nil
}
