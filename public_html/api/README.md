# DappDappGo.coolhd.hu API

### Add Skylink:

`POST https://dappdappgo.coolhd.hu/api/add_skylink.php`

Data: `skylink=_A2zt5SKoqwnnZU4cBF8uBycSKULXMyeg1c5ZISBr2Q3dA` or `skylink=sia://_A2zt5SKoqwnnZU4cBF8uBycSKULXMyeg1c5ZISBr2Q3dA`

Response:

``` json
{'error':'wrong_skylink','msg':'The submitted skylink is invalid, please submit 'sia:{46 char}' or '{46 char}' via POST. Please do NOT submit urls with portal domains.'}
```
or
``` json
{'skylink':'_A2zt5SKoqwnnZU4cBF8uBycSKULXMyeg1c5ZISBr2Q3dA','msg':'Skylink added successfully to the updater queue. Thank you!'}
```