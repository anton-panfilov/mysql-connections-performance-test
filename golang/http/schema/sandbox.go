package schema

import "time"

type Sandbox struct {
	ID            uint      `db:"id"`
	GUID          []byte    `db:"guid"`
	Created       time.Time `db:"created"`
	Enum          int8      `db:"enum"`
	Int           int       `db:"int"`
	String        string    `db:"string"`
	Bool          bool      `db:"bool"`
	JSON          string    `db:"json"`
	EncryptedJSON []byte    `db:"encrypted_json"`
}
