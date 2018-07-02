package main

import (
    "github.com/gin-gonic/gin"
    "net/http"
		"io/ioutil"
		"os"
)

func getCoupons(c *gin.Context)  {
	req, err := http.NewRequest("GET","http://nginx/get-coupons", nil)

  if err != nil {
			displayError(c)
  }

	query := req.URL.Query()

	if brand := c.Query("brand"); brand != "" {
		query.Add("brand", brand)
	}

	if value := c.Query("value"); value != "" {
		query.Add("value", value)
	}

	query.Add("limit", c.DefaultQuery("limit", "3"))
	req.URL.RawQuery = query.Encode()
	client := &http.Client{}
	response, err := client.Do(req)

	if err != nil {
		displayError(c)
	}

	data, _ := ioutil.ReadAll(response.Body)
	c.Data(http.StatusOK, "application/json", data)
}

func displayError(c *gin.Context)  {
	c.JSON(http.StatusInternalServerError, gin.H{
		"message": "Internal server error",
	})
	os.Exit(1)
}

func main() {
	router := gin.Default()
	router.GET("/coupons", getCoupons)
	router.Run(":8080")
}
