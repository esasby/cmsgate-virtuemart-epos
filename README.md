## Модуль интеграции с CMS Joomla 3.x (с корзиной Virtuemart 3.8)

Данный модуль обеспечивает взаимодействие между интернет-магазином на базе CMS **Joomla Virtuemart** и сервисом платежей [E-POS](www.e-pos.by).

### Инструкция по установке:
### Ручная установка
1. Создайте резервную копию вашего магазина и базы данных
1. Загрузите архив с модулем [epos.zip](https://bitbucket.org/esasby/cmsgate-virtuemart-epos/raw/master/epos.zip)
1. В административной части Joomla выберите `Расширения - Менеджер расширений - Установка - Загрузить Файл пакета`. Выберите архив и нажмите `Загрузить`.

## Инструкция по настройке
1. Перейдите в меню `Компоненты — Virtuemart - Payment Methods`.
1. Выберите Epos, перейдите на вкладку "Конфигурация"
1. Задайте параметры для модуля
    * EPOS процессинг - выбор организации, выполняющей интеграцию с EPOS
    * Идентификатор клиента – Ваш персональный логи для работы с сервисом EPOS
    * Секрет – Ваш секретный ключ для работы с сервисом EPOS
    * Код ПУ – код поставщика услуги в системе EPOS
    * Код услуги EPOS – код услуги у поставщика услуг в системе EPOS (один ПУ может предоставлять несколько разных услуг)
    * Код торговой точки – код торговой точки ПУ (у одного ПУ может быть несколько торговых точек)
    * Debug mode - запись и отображение дополнительных сообщений при работе модуля
    * Sandbox - перевод модуля в тестовый режим работы. В этом режиме счета выставляются в тестовую систему
    * Использовать номер заказа - если включен, то в ЕРИП будет выставлен счет с локальным номером заказа (orderNumber), иначе с локальным идентификатором (orderId)
    * Срок действия счета - как долго счет, будет доступен в ЕРИП для оплаты    
    * Статус при выставлении счета  - какой статус выставить заказу при успешном выставлении счета в ЕРИП (идентификатор существующего статуса из Магазин > Настройки > Статусы)
    * Статус при успешной оплате счета - какой статус выставить заказу при успешной оплате выставленного счета (идентификатор существующего статуса)
    * Статус при отмене оплаты счета - какой статус выставить заказу при отмене оплаты счета (идентификатор существующего статуса)
    * Статус при ошибке оплаты счета - какой статус выставить заказу при ошибке выставленния счета (идентификатор существующего статуса)
    * Секция "Инструкция" - если включена, то на итоговом экране клиенту будет доступна пошаговая инструкция по оплате счета в ЕРИП
    * Секция QR-code - если включена, то на итоговом экране клиенту будет доступна оплата счета по QR-коду
    * Секция Webpay - если включена, то на итоговом экране клиенту отобразится кнопка для оплаты счета картой (переход на Webpay)
    * Текст успешного выставления счета - текст, отображаемый кленту после успешного выставления счета. Может содержать html. В тексте допустимо ссылаться на переменные @order_id, @order_number, @order_total, @order_currency, @order_fullname, @order_phone, @order_address
1. Сохраните изменения.


### Внимание!
* Для автоматического обновления статуса заказа (после оплаты клиентом выставленного в ЕРИП счета) необходимо сообщить в службу технической поддержки сервиса «Хуткi Грош» адрес обработчика:
```
http://mydomen.my/index.php?option=com_virtuemart&view=epos&task=callback
```
* Модуль ведет лог файл по пути _site_root/plugins/vmpayment/epos/vendor/esas/cmsgate-core/logs/cmsgate.log_
Для обеспечения **безопасности** необходимо убедиться, что в настройках http-сервера включена директива _AllowOverride All_ для корневой папки.

### Тестовые данные
Для настрой оплаты в тестовом режиме:
 * воспользуйтесь данными для подключения к тестовой системе, полученными при регистрации в EPOS
 * включите в настройках модуля режим "Песочницы" ("Sandbox")
 
_Разработано и протестировано с Joomla v3.9.23 + Virtuemart v3.8.6_
