package main

import (
    "github.com/gin-gonic/gin"
    "net/http"
    "io/ioutil"
    "github.com/gin-gonic/gin/binding"
    "gopkg.in/go-playground/validator.v9"
    "github.com/google/go-querystring/query"
)

const ApiKey = "adfhg@##$dfadas2312312789FSDADF342"

type Query struct {
  Brand string `url:"brand" form:"brand"`
  Value int    `url:"value" form:"value" validate:"gte=0,lte=100"`
  Limit int    `url:"limit" form:"limit" validate:"gte=0,lte=100"`
}

var validate *validator.Validate

func main() {
  router := gin.Default()
  router.GET("/coupons", getCoupons)
  router.Run(":8080")
}

func getCoupons(c *gin.Context)  {
  var queryStruct Query

  prepareData(c, &queryStruct)
  validateData(c, &queryStruct)
  response := createRequest(c, &queryStruct)

  data, _ := ioutil.ReadAll(response.Body)
  c.Data(http.StatusOK, "application/json", data)
}

func prepareData(c *gin.Context, queryStruct *Query)  {
  if err := c.ShouldBindWith(queryStruct, binding.Query); err != nil {
    c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
    return
  }
}

func validateData(c *gin.Context, queryStruct *Query)  {
  validate = validator.New()
  if err := validate.Struct(queryStruct); err != nil {
    c.JSON(http.StatusBadRequest, gin.H{"error": "Data provided is incomplete or wrong"})
    return
  }
}

func createRequest(c *gin.Context, queryStruct *Query) (*http.Response) {
  req, err := http.NewRequest("GET","http://nginx/get-coupons", nil)
  req.Header.Set("x-api-key", ApiKey)

  if err != nil {
    displayError(c)
  }

  v, _ := query.Values(queryStruct)
  req.URL.RawQuery = v.Encode()
  client := &http.Client{}
  response, err := client.Do(req)

  if err != nil {
    displayError(c)
  }

  return response
}

func displayError(c *gin.Context)  {
  c.JSON(http.StatusInternalServerError, gin.H{
    "message": "Internal server error",
  })

  return
}
