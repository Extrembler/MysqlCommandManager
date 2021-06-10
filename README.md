# MysqlCommandManager

Run Basic mysql commands from magento 2 terminal

## How it works ##

bin/magento sql:runQueryDeveloperOnly --query "select * from sales_order where increment_id = '1000001' LIMIT 1"

# caution 
Always use limit, needed columns , where condition in your query for better result

# version support
  1.0.0
    support only select, update and show
