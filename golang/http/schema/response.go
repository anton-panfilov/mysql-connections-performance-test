package schema

const Language = "golang"
const TestSelect = "select"

type ResponseSelect struct {
	Language  string  `json:"language"`
	Test      string  `json:"test"`
	Driver    string  `json:"driver"`
	Method    string  `json:"method"`
	Threads   uint8   `json:"threads"`
	BatchSize uint32  `json:"batch_size"`
	DataSize  uint32  `json:"data_size"`
	Columns   uint8   `json:"columns"`
	Duration  float64 `json:"duration"`
}
