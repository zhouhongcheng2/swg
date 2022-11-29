<?php
class ESTConfig
{
    //商户号
    const mchId = "240554510258";
    //商户私钥
    const priKey = "MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCxDNeGzooChPoeUjw6UWdv5ZjkwvRQReRxUfqCnLYdEsSkRmGN1UXLISyF52bbNyqVB6+nxFuzdCuIDSshLnExL/7kagHaEE9L/s+GoyyVbeUkIgOSTQjuFlbS++eIW/lrq9UXbqHG0kWqdvd9ycZNNkGEqteABGlWcF1NTUaMRHor8GTfK3dudbiw2UAqJYAG3qjzTAp1ppSuzEyePChVBmp2AVJ3xlZamJZBHDfCL5WKE/t3/LOB/8mFrBNClzyBUuEcZNKpSJ2hhZDRwkkKOowCQA1HwDaQPvF8xNDvNu6yN6wt999FA7IGLPudWjZCfootZSNS481mKpts/FFZAgMBAAECggEBAIgC34d2H1t0IFkuv4nlg1rYvK3wfpM0Phw36ARyswx+oIW6c7LrxiQYJgXwEHoTVSkLsItnMzMW6WIpC5r//IDW6C88qJOGuAQfiflaXSOmOsOZRbkcaOHOU4Ddd66vSVrHtHm9yZXdbxtXLSV63lXuekKao9Z6jRmUVHjQBoQ1x4Xccoi0lOCe9aYkRzL8jc8IxIGilCet8L3Yuru8VTCxcCTkHKpUqijKb83LNYFPhkhCjysJ1JX7gpz+iYWrLG3vNy7ypfjBAHEVYwrpEr+eHlgThnO/2krbr02h8VuX7thcklpqXMeApPtLrOkj3i3yhnjomUbeFbPXR/8nNwECgYEA2/4s6uMMxDm2RlpOD3micvQBM+Bu1si0tiD3lifCiqtLY1RKSKW+nhnNsCgmPGFiHnZrCe0HDqhf3YrfPzPMbPZ3st3369zleZNOeH07HLn29qs+99eJSkGO06VKG3aN2AyGywdA+rQafhA/SyOFlYb6ACMjkrI1ON1v05JfOWkCgYEAzgdXxzUGAqKHn8pqs5tZpX+GS7jREhBKmOIMW6VKZMI8dm0pgoq0LHwJrEL8nbkxFsBOLrmvFC2N24XNiaAP0kCHNurvAc/cPq3eee00OzqWjrqGXTq7HipaHHjn2CG78NMsJu4ggiz05US1j+iatx3lJgZQpUO5gnklDdlq6nECgYEAxXnDPlHz6szXdw2nFrMcBM5NqhCjzj+6H+c1+F7Wff3HnXFTgSTvGCKXhbJ0qCzOlAO+j9lqRVkVPNxlC+nmbkVMK46e9uWCRADcJsJbZqz0KHaN/pJG1hZFLG0qOb7REwgjk2p+hM1ZHqJc/ojzZ8cS8T6ZtxwrNFFqCWRSAEkCgYA6cP7HNRU1XiEWhHZr6B8vwNK5W/2CIDjo0QYsYOCPYEGCXkiuDOY60Bx2TrIVk1iHWZlTy7wL/zgEExpQdaTHQSKZw150px+UcAFxmIV+X4ohhmtiuqwlTGIvPWSWfEsxbtVRXa6+/j2hYzIVVie81bAQby4lEubmSbLnEMsxIQKBgQDKstTrHGg5y64oMbqgN4N/gSrUBSQiRx2j9aiMdIIUrftmjFr11pP/VM2Z7xZpJsNxTIBcbmyhll5nAsHo9/dQX4MwLSbqr1huP3UA/YtffnpeUQxK2KAxKV6FF5OZW1xEVOw7P+6Bd7tTyAPc9DF00c42a7qRlzLtMps8zZn5gg==";
    //商户公钥
    const pubKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA05d7oNP9LFsUUjhkXfRI8Xo2F8ZZnZAZMsyn7zTQOZUOpsIGTmaulLXroOHghWD/XkA2rvEaKuxwxjMuQ9oiGctt+oLH9CaYC/sI9rTO0hexTRQLl7lPqLjFOtLCMOx7u67qrQhcZzLWj8IrKORId6kt7UvmDGypKx3O0dX17F927D4kK7svliYchjR0P4I8M/xy+MujnH+15o7/g3FptntpUoFV4yZmehbBszHupVyOhHt04kIF0Toc0eXlQz63OPLmr07PCUbY/oOCOgQUVqJWrg0gowmZWHL38RPey6szPjXxxg/38nTAijT2YCgDzGTWRiMI894EXStMonz+9wIDAQAB";

    //统一下单
    const payCreateUrl = "/ecp/ecpserver/pay/api/pay/create";
    //二级商户到清分账户
    const refundShareCreateUrl = "/ecp/ecpserver/open/api/shareAllocation/refund/v2";

    //支付查询
    const payQueryUrl = "/ecp/ecpserver/pay/api/pay/query";
    //统一退款
    const refundCreateUrl = "/ecp/ecpserver/pay/api/refund/create";
    //退款查询
    const refundQueryUrl = "/ecp/ecpserver/pay/api/refund/query";
    //分账-子订单查询
    const shareAllocationQuery = "/ecp/ecpserver/open/api/shareAllocation/query/v2";
    //分账-子订单录入
    const shareAllocationApply = "/ecp/ecpserver/open/api/shareAllocation/apply/v2";
}