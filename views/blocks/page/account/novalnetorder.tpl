[{if $oView->novalnetGetTheme() == 'flow'}]
    [{if count($oOrders) > 0}]
        [{assign var=oArticleList value=$oView->getOrderArticleList()}]
        <ol class="list-unstyled">
            [{foreach from=$oOrders item=order}]
                <li>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <strong>[{oxmultilang ident="DD_ORDER_ORDERDATE"}]</strong>
                                    <span id="accOrderDate_[{$order->oxorder__oxordernr->value}]">[{$order->oxorder__oxorderdate->value|date_format:"%d.%m.%Y"}]</span>
                                    <span>[{$order->oxorder__oxorderdate->value|date_format:"%H:%M:%S"}]</span>
                                </div>
                                <div class="col-xs-3">
                                    <strong>[{oxmultilang ident="STATUS"}]</strong>
                                    <span id="accOrderStatus_[{$order->oxorder__oxordernr->value}]">
                                        [{if $order->oxorder__oxstorno->value}]
                                            <span class="note">[{oxmultilang ident="ORDER_IS_CANCELED"}]</span>
                                        [{elseif $order->oxorder__oxsenddate->value !="-"}]
                                            <span>[{oxmultilang ident="SHIPPED"}]</span>
                                        [{else}]
                                            <span class="note">[{oxmultilang ident="NOT_SHIPPED_YET"}]</span>
                                        [{/if}]
                                    </span>
                                </div>
                                <div class="col-xs-3">
                                    <strong>[{oxmultilang ident="ORDER_NUMBER"}]</strong>
                                    <span id="accOrderNo_[{$order->oxorder__oxordernr->value}]">[{$order->oxorder__oxordernr->value}]</span>
                                </div>
                                <div class="col-xs-3">
                                    <strong>[{oxmultilang ident="SHIPMENT_TO"}]</strong>
                                        <span id="accOrderName_[{$order->oxorder__oxordernr->value}]">
                                        [{if $order->oxorder__oxdellname->value}]
                                            [{$order->oxorder__oxdelfname->value}]
                                            [{$order->oxorder__oxdellname->value}]
                                        [{else}]
                                            [{$order->oxorder__oxbillfname->value}]
                                            [{$order->oxorder__oxbilllname->value}]
                                        [{/if}]
                                    </span>
                                    [{if $order->getShipmentTrackingUrl()}]
                                        &nbsp;|&nbsp;<strong>[{oxmultilang ident="TRACKING_ID"}]</strong>
                                        <span id="accOrderTrack_[{$order->oxorder__oxordernr->value}]">
                                            <a href="[{$order->getShipmentTrackingUrl()}]">[{oxmultilang ident="TRACK_SHIPMENT"}]</a>
                                        </span>
                                    [{/if}]
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <strong>[{oxmultilang ident="CART"}]</strong>
                            <ol class="list-unstyled">
                                [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
                                    [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value}]
                                    [{assign var=oArticle value=$oArticleList[$sArticleId]}]
                                    <li id="accOrderAmount_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]">
                                        [{$orderitem->oxorderarticles__oxamount->value}] [{oxmultilang ident="QNT"}]
                                        [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible()}]
                                            <a id="accOrderLink_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{$oArticle->getLink()}]">
                                        [{/if}]
                                        [{$orderitem->oxorderarticles__oxtitle->value}] [{$orderitem->oxorderarticles__oxselvariant->value}] <span class="amount"></span>
                                        [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible()}]
                                            </a>
                                        [{/if}]
                                        [{foreach key=sVar from=$orderitem->getPersParams() item=aParam}]
                                            [{if $aParam}]
                                                <br />[{oxmultilang ident="DETAILS"}]: [{$aParam}]
                                            [{/if}]
                                        [{/foreach}]
                                        [{* Commented due to Trusted Shops precertification. Enable if needed *}]
                                        [{*
                                        [{oxhasrights ident="TOBASKET"}]
                                        [{if $oArticle->isBuyable()}]
                                          [{if $oArticle->oxarticles__oxid->value}]
                                            <a id="accOrderToBasket_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" params="fnc=tobasket&amp;aid=`$oArticle->oxarticles__oxid->value`&amp;am=1"}]">[{oxmultilang ident="TO_CART"}]</a>
                                          [{/if}]
                                        [{/if}]
                                        [{/oxhasrights}]
                                        *}]
                                    </li>
                                [{/foreach}]
                            </ol>
                        </div>
                        <!-- Novalnet code begins -->
                        [{if in_array($order->oxorder__oxpaymenttype->value, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetonlinetransfer', 'novalnetideal', 'novalnetpaypal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24','novalnetbarzahlen')) }]
                            <div class="panel-body">
                                <strong>[{ oxmultilang ident="NOVALNET_PAYMENT_TYPE" suffix="COLON" }]</strong>
                                <ol class="list-unstyled">
                                    <li id="accOrderNovalnetPaymentType_[{$order->oxorder__oxordernr->value}]">
                                       [{$oView->getNovalnetPaymentName($order->oxorder__oxpaymenttype->value)|html_entity_decode}]
                                    </li>
                                </ol>
                            </div>
                            [{if in_array($order->oxorder__oxpaymenttype->value, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetpaypal')) && $oView->getNovalnetSubscriptionStatus($order->oxorder__oxordernr->value)}]
                                [{oxscript add="$('.novalnetSubsForm').submit(function(){
                                    if ($(this).find('#cancelReason').val() == '') {
                                         alert($(this).find('#novalnetInvalidReason').val());
                                    return false;
                                    } else {
                                        if (!confirm($(this).find('#novalnetCancelReason').val())) {
                                            return false;
                                        }
                                    }
                                    });"}]
                                <div class="panel-body">
                                    <ol class="list-unstyled">
                                        <strong>[{ oxmultilang ident="NOVALNET_SUBSCRIPTION_CANCEL_TITLE" suffix="COLON" }]</strong>
                                        <li id="accOrderNovalnetSubscriptionCancel_[{$order->oxorder__oxordernr->value}]">
                                            <form action="[{ $oViewConf->getSelfActionLink() }]" class="novalnetSubsForm" method="post">
                                                [{ $oViewConf->getHiddenSid() }]
                                                <input type="hidden" name="fnc" value="cancelNovalnetSuscription">
                                                <input type="hidden" name="cl" value="account_order">
                                                <input type="hidden" name="novalnet[iOrderNo]" value="[{$order->oxorder__oxordernr->value}]">
                                                <input type="hidden" id="novalnetInvalidReason" value="[{oxmultilang ident='NOVALNET_INVALID_CANCEL_REASON'}]">
                                                <input type="hidden" id="novalnetCancelReason" value="[{oxmultilang ident='NOVALNET_CANCEL_REASON'}]">
                                                <label for="status">[{oxmultilang ident="NOVALNET_SUBSCRIPTION_CANCEL_LABEL" suffix="COLON" }]</label>
                                                <select id="cancelReason" name="novalnet[sCancelReason]" class="selectpicker">
                                                    <option value=''>[{ oxmultilang ident="NOVALNET_PLEASE_SELECT" }]</option>
                                                    [{foreach from=$oView->getNovalnetSubsReasons() item=reason}]
                                                        <option value='[{$reason}]'>[{$reason}]</option>
                                                    [{/foreach}]
                                                </select>
                                                <button type="submit" class="btn btn-primary">[{oxmultilang ident="NOVALNET_UPDATE" }]</button>
                                            </form>
                                        </li>
                                    </ol>
                                </div>
                            [{/if}]
                            <div class="panel-body">
                                <ol class="list-unstyled">
                                    <li id="accOrderNovalnetTransactionDetails_[{$order->oxorder__oxordernr->value}]">
                                        [{if $order->oxorder__novalnet_transaction->value != ''}]
                                            [{ oxmultilang ident="NOVALNET_TRANSACTION_DETAILS" }]<br>
                                            [{$order->oxorder__novalnet_transaction->value|html_entity_decode}]
                                        [{/if}]
                                        [{if $order->oxorder__novalnet_comments->value != ''}]
                                            [{$order->oxorder__novalnet_comments->value|html_entity_decode}]
                                        [{/if}]
                                        [{$order->oxorder__novalnetcomments->value|html_entity_decode}]
                                    </li>
                                </ol>
                            </div>
                        [{/if}]
                        <!-- Novalnet code ends -->
                    </div>
                </li>
            [{/foreach}]
        </ol>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
    [{else}]
        [{oxmultilang ident="ORDER_EMPTY_HISTORY"}]
    [{/if}]
[{elseif $oView->novalnetGetTheme() == 'mobile'}]
    [{if count($oOrders) > 0}]
        [{assign var=oArticleList value=$oView->getOrderArticleList()}]
        <ul id="orderList" class="order-history-list">
            [{foreach from=$oOrders item=order}]
                <li>
                    <ul class="order-history-details">
                        <li>
                            <span id="accOrderDate_[{$order->oxorder__oxordernr->value}]" title="[{oxmultilang ident="ORDER_DATE"}]" >[{$order->oxorder__oxorderdate->value|date_format:"%d.%m.%Y"}]</span>
                            <strong>[{oxmultilang ident="ORDER_NUMBER"}]:</strong>
                            <span id="accOrderNo_[{$order->oxorder__oxordernr->value}]">[{$order->oxorder__oxordernr->value}]</span>
                        </li>
                        <li>
                            <strong>[{oxmultilang ident="STATUS"}]</strong>
                            <span id="accOrderStatus_[{$order->oxorder__oxordernr->value}]">
                                [{if $order->oxorder__oxstorno->value}]
                                    <span class="note">[{oxmultilang ident="ORDER_IS_CANCELED"}]</span>
                                [{elseif $order->oxorder__oxsenddate->value !="-" }]
                                    <span>[{oxmultilang ident="SHIPPED"}]</span>
                                [{else}]
                                    <span class="note">[{oxmultilang ident="NOT_SHIPPED_YET"}]</span>
                                [{/if}]
                            </span>
                        </li>
                        [{if $order->getShipmentTrackingUrl()}]
                            <li>
                                <strong>[{oxmultilang ident="TRACKING_ID"}]</strong>
                                <span id="accOrderTrack_[{$order->oxorder__oxordernr->value}]">
                                    <a href="[{$order->getShipmentTrackingUrl()}]">[{oxmultilang ident="TRACK_SHIPMENT"}]</a>
                                </span>
                            </li>
                        [{/if}]
                        <li>
                            <strong>[{oxmultilang ident="SHIPMENT_TO"}]</strong>
                            <span id="accOrderName_[{$order->oxorder__oxordernr->value}]">
                            [{if $order->oxorder__oxdellname->value }]
                                [{$order->oxorder__oxdelfname->value}]
                                [{$order->oxorder__oxdellname->value}]
                            [{else }]
                                [{$order->oxorder__oxbillfname->value}]
                                [{$order->oxorder__oxbilllname->value}]
                            [{/if}]
                            </span>
                        </li>
                        <!-- Novalnet code begins -->
                        [{if in_array($order->oxorder__oxpaymenttype->value, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetonlinetransfer', 'novalnetideal', 'novalnetpaypal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24','novalnetbarzahlen')) }]
                            <li>
                                <strong>[{oxmultilang ident="NOVALNET_PAYMENT_TYPE"}]</strong>
                                <span id="accOrderNovalnetPaymentType_[{$order->oxorder__oxordernr->value}]">
                                    [{$oView->getNovalnetPaymentName($order->oxorder__oxpaymenttype->value)|html_entity_decode}]
                                </span>
                            </li>
                            [{if in_array($order->oxorder__oxpaymenttype->value, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetpaypal')) && $oView->getNovalnetSubscriptionStatus($order->oxorder__oxordernr->value)}]
                                [{oxscript include="js/widgets/oxdropdown.js" priority=10 }]
                                [{oxscript add="$('.novalnetSubsCancel').oxDropDown();"}]
                                [{oxscript add="$('.novalnetSubsForm').submit(function(){
                                    if ($(this).find('#cancelReason').val() == '')
                                    {
                                        alert($(this).find('#novalnetInvalidReason').val());
                                        return false;
                                    } else {
                                        if (!confirm($(this).find('#novalnetCancelReason').val())) {
                                            return false;
                                        }
                                    }
                                });"}]
                                <li>
                                    <strong>[{oxmultilang ident="NOVALNET_SUBSCRIPTION_CANCEL_TITLE"}]</strong><br><br>
                                    <span id="accOrderNovalnetCancelSubs_[{$order->oxorder__oxordernr->value}]" >[{oxmultilang ident="NOVALNET_SUBSCRIPTION_CANCEL_LABEL" suffix="COLON"}]</span><br><br>
                                    <form action="[{ $oViewConf->getSelfActionLink() }]" class="novalnetSubsForm" method="post">
                                        [{ $oViewConf->getHiddenSid() }]
                                        <input type="hidden" name="fnc" value="cancelNovalnetSuscription">
                                        <input type="hidden" name="cl" value="account_order">
                                        <input type="hidden" name="novalnet[iOrderNo]" value="[{$order->oxorder__oxordernr->value}]">
                                        <input type="hidden" id="novalnetInvalidReason" value="[{oxmultilang ident='NOVALNET_INVALID_CANCEL_REASON'}]">
                                        <input type="hidden" id="novalnetCancelReason" value="[{oxmultilang ident='NOVALNET_CANCEL_REASON'}]">
                                        <div class="dropdown novalnetSubsCancel">
                                            [{assign var=aReasons value=$oView->getNovalnetSubsReasons()}]
                                            <input type="hidden" id="cancelReason" name="novalnet[sCancelReason]" />
                                            <div class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                                                <a id="novalnetReasonSelected" role="button" href="#">
                                                    <span id="dNovalnetReasonSelected" style="color:#000000;">[{oxmultilang ident="NOVALNET_PLEASE_SELECT"}]</span>
                                                    <i class="glyphicon-chevron-down" style="color:#000000;"></i>
                                                </a>
                                            </div>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="novalnetReasonSelected">
                                                <li class="dropdown-option">
                                                    <a tabindex="-1" data-selection-id="">[{oxmultilang ident="NOVALNET_PLEASE_SELECT"}]</a>
                                                </li>
                                                [{foreach from=$aReasons item=sReason}]
                                                    <li class="dropdown-option">
                                                        <a tabindex="-1" data-selection-id="[{$sReason}]">[{$sReason}]</a>
                                                    </li>
                                                [{/foreach}]
                                            </ul>
                                        </div><br>
                                        <button type="submit" class="submitButton largeButton btn">[{oxmultilang ident="NOVALNET_UPDATE" }]</button>
                                    </form>
                                </li>
                            [{/if}]
                            <li>
                                <span id="accOrderNovalnetTransactionDetails_[{$order->oxorder__oxordernr->value}]">
                                    [{if $order->oxorder__novalnet_transaction->value != ''}]
                                        [{ oxmultilang ident="NOVALNET_TRANSACTION_DETAILS" }]<br>
                                        [{$order->oxorder__novalnet_transaction->value|html_entity_decode}]
                                    [{/if}]
                                    [{if $order->oxorder__novalnet_comments->value != ''}]
                                        [{$order->oxorder__novalnet_comments->value|html_entity_decode}]
                                    [{/if}]
                                    [{ $order->oxorder__novalnetcomments->value|html_entity_decode }]
                                </span>
                            </li>
                        [{/if}]
                        <!-- Novalnet code ends -->
                    </ul>
                    <ul class="order-history-articles">
                        [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
                            <li>
                                [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value }]
                                [{assign var=oArticle value=$oArticleList[$sArticleId] }]
                                    <span class="order-history-article-quantity" title="[{oxmultilang ident="QNT"}]">[{$orderitem->oxorderarticles__oxamount->value}]</span>
                                    [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]
                                        <a  id="accOrderLink_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{$oArticle->getLink()}]">
                                    [{/if}]
                                        [{$orderitem->oxorderarticles__oxtitle->value}]
                                        [{if $orderitem->oxorderarticles__oxselvariant->value}]
                                            <br /><span class="variants">[{$orderitem->oxorderarticles__oxselvariant->value}]</span>
                                        [{/if}]
                                    [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]</a>[{/if}]
                                    [{* Commented due to Trusted Shops precertification. Enable if needed *}]
                                    [{*
                                    [{oxhasrights ident="TOBASKET"}]
                                    [{if $oArticle->oxarticles__oxid->value && $oArticle->isBuyable() }]
                                        <a id="accOrderToBasket_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" params="fnc=tobasket&amp;aid=`$oArticle->oxarticles__oxid->value`&amp;am=1"}]" rel="nofollow">[{oxmultilang ident="TO_CART"}]</a>
                                    [{/if}]
                                    [{/oxhasrights}]
                                    *}]
                                [{if 'EE' == $oView->getEdition()}]
                                    [{if $orderitem->getStatus()}]
                                        <div class="article-details">
                                            <strong>[{oxmultilang ident="DELIVERY_STATUS"}]</strong>
                                            <ul>
                                                [{foreach from=$orderitem->getStatus() item=aStatus }]
                                                    <li>
                                                        <strong>[{if $aStatus->STATUS == "ANG"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_ANG"}]
                                                        [{elseif $aStatus->STATUS == "HAL"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_HAL"}]
                                                        [{elseif $aStatus->STATUS == "BES"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_BES"}]
                                                        [{elseif $aStatus->STATUS == "EIN"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_EIN"}]
                                                        [{elseif $aStatus->STATUS == "AUS"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_AUS"}]
                                                        [{elseif $aStatus->STATUS == "STO"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_STO"}]
                                                        [{elseif $aStatus->STATUS == "NLB"}]
                                                          [{oxmultilang ident="DELIVERY_STATUS_NLB"}]
                                                        [{else}]
                                                          [{$aStatus->STATUS}]
                                                        [{/if}]</strong>
                                                        <span>([{$aStatus->date|date_format:"%d.%m.%Y %H:%M"}]) </span>
                                                    </li>
                                                [{/foreach}]
                                            </ul>
                                            [{if $aStatus->trackingid }]
                                                <strong>[{oxmultilang ident="TRACKING_ID"}]</strong>
                                                <span>[{$aStatus->trackingid}]</span>
                                            [{/if}]
                                        </div>
                                    [{/if}]
                                [{/if}]
                            </li>
                        [{/foreach}]
                    </ul>
                </li>
            [{/foreach}]
        </ul>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
    [{else}]
        <div class="content">
            [{oxmultilang ident="ORDER_EMPTY_HISTORY"}]
        </div>
    [{/if}]
[{else}]
    [{if count($oOrders) > 0}]
        [{assign var=oArticleList value=$oView->getOrderArticleList()}]
        <ul class="orderList">
            [{foreach from=$oOrders item=order}]
                <li>
                    <table class="orderitems">
                        <tr>
                            <td>
                                <dl>
                                    <dt title="[{oxmultilang ident="ORDER_DATE" suffix="COLON"}]">
                                        <strong id="accOrderDate_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxorderdate->value|date_format:"%d.%m.%Y" }]</strong>
                                        <span>[{ $order->oxorder__oxorderdate->value|date_format:"%H:%M:%S" }]</span>
                                    </dt>
                                    <dd>
                                        <strong>[{ oxmultilang ident="STATUS" suffix="COLON" }]</strong>
                                        <span id="accOrderStatus_[{$order->oxorder__oxordernr->value}]">
                                            [{if $order->oxorder__oxstorno->value}]
                                                <span class="note">[{ oxmultilang ident="ORDER_IS_CANCELED" }]</span>
                                            [{elseif $order->oxorder__oxsenddate->value !="-" }]
                                                <span>[{ oxmultilang ident="SHIPPED" }]</span>
                                            [{else}]
                                                <span class="note">[{ oxmultilang ident="NOT_SHIPPED_YET" }]</span>
                                            [{/if}]
                                        </span>
                                    </dd>
                                    <dd>
                                        <strong>[{ oxmultilang ident="ORDER_NUMBER" suffix="COLON" }]</strong>
                                        <span id="accOrderNo_[{$order->oxorder__oxordernr->value}]">[{ $order->oxorder__oxordernr->value }]</span>
                                    </dd>
                                    [{if $order->getShipmentTrackingUrl()}]
                                        <dd>
                                            <strong>[{ oxmultilang ident="TRACKING_ID" suffix="COLON" }]</strong>
                                            <span id="accOrderTrack_[{$order->oxorder__oxordernr->value}]">
                                                <a href="[{$order->getShipmentTrackingUrl()}]">[{ oxmultilang ident="TRACK_SHIPMENT" }]</a>
                                            </span>
                                        </dd>
                                    [{/if}]
                                    <dd>
                                        <strong>[{ oxmultilang ident="SHIPMENT_TO" suffix="COLON" }]</strong>
                                        <span id="accOrderName_[{$order->oxorder__oxordernr->value}]">
                                        [{if $order->oxorder__oxdellname->value }]
                                            [{ $order->oxorder__oxdelfname->value }]
                                            [{ $order->oxorder__oxdellname->value }]
                                        [{else }]
                                            [{ $order->oxorder__oxbillfname->value }]
                                            [{ $order->oxorder__oxbilllname->value }]
                                        [{/if}]
                                        </span>
                                    </dd>
                                </dl>
                            </td>
                            <td>
                                <h3>[{ oxmultilang ident="CART" suffix="COLON" }]</h3>
                                <table class="orderhistory">
                                    [{foreach from=$order->getOrderArticles(true) item=orderitem name=testOrderItem}]
                                        [{assign var=sArticleId value=$orderitem->oxorderarticles__oxartid->value }]
                                        [{assign var=oArticle value=$oArticleList[$sArticleId] }]
                                        <tr id="accOrderAmount_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]">
                                          <td>
                                            [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]
                                                <a  id="accOrderLink_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{$oArticle->getLink()}]">
                                            [{/if}]
                                                [{ $orderitem->oxorderarticles__oxtitle->value }] [{ $orderitem->oxorderarticles__oxselvariant->value }] <span class="amount"> - [{ $orderitem->oxorderarticles__oxamount->value }] [{oxmultilang ident="QNT"}]</span>
                                            [{if $oArticle->oxarticles__oxid->value && $oArticle->isVisible() }]</a>[{/if}]
                                            [{foreach key=sVar from=$orderitem->getPersParams() item=aParam}]
                                                [{if $aParam }]
                                                <br />[{ oxmultilang ident="DETAILS" suffix="COLON" }] [{$aParam}]
                                                [{/if}]
                                            [{/foreach}]
                                          </td>
                                          <td class="small">
                                            [{* Commented due to Trusted Shops precertification. Enable if needed *}]
                                            [{*
                                            [{oxhasrights ident="TOBASKET"}]
                                            [{if $oArticle->oxarticles__oxid->value && $oArticle->isBuyable() }]
                                                <a id="accOrderToBasket_[{$order->oxorder__oxordernr->value}]_[{$smarty.foreach.testOrderItem.iteration}]" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_order" params="fnc=tobasket&amp;aid=`$oArticle->oxarticles__oxid->value`&amp;am=1" }]" rel="nofollow">[{ oxmultilang ident="TO_CART" }]</a>
                                            [{/if}]
                                            [{/oxhasrights}]
                                            *}]
                                          </td>
                                        </tr>

                                    [{/foreach}]
                                </table>
                          </td>
                        </tr>
                        <!-- Novalnet code begins -->
                        [{if in_array($order->oxorder__oxpaymenttype->value, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetonlinetransfer', 'novalnetideal', 'novalnetpaypal', 'novalneteps', 'novalnetgiropay', 'novalnetprzelewy24','novalnetbarzahlen')) }]
                            <tr>
                                <td colspan="2">
                                    <strong>[{ oxmultilang ident="NOVALNET_PAYMENT_TYPE" suffix="COLON" }]</strong>
                                    <span id="accOrderNovalnetPaymentType_[{$order->oxorder__oxordernr->value}]">[{$oView->getNovalnetPaymentName($order->oxorder__oxpaymenttype->value)|html_entity_decode}]</span>
                                </td>
                            </tr>
                            [{if in_array($order->oxorder__oxpaymenttype->value, array('novalnetcreditcard', 'novalnetsepa', 'novalnetinvoice', 'novalnetprepayment', 'novalnetpaypal')) && $oView->getNovalnetSubscriptionStatus($order->oxorder__oxordernr->value)}]
                                <tr>
                                    <td colspan="2">
                                        [{oxscript add="$('.novalnetSubsForm').submit(function(){
                                            if ($(this).find('#cancelReason').val() == '')
                                            {
                                                alert($(this).find('#novalnetInvalidReason').val()); return false;
                                             }
                                              else {
                                                if (!confirm($(this).find('#novalnetCancelReason').val())) {
                                                    return false;
                                                }
                                            }
                                             });"}]
                                        <h3>[{ oxmultilang ident="NOVALNET_SUBSCRIPTION_CANCEL_TITLE" }]</h3>
                                        <table>
                                            <tr>
                                                <td>
                                                    <form action="[{ $oViewConf->getSelfActionLink() }]" class="novalnetSubsForm" method="post">
                                                    [{ $oViewConf->getHiddenSid() }]
                                                        <input type="hidden" name="fnc" value="cancelNovalnetSuscription">
                                                        <input type="hidden" name="cl" value="account_order">
                                                        <input type="hidden" name="novalnet[iOrderNo]" value="[{$order->oxorder__oxordernr->value}]">
                                                        <input type="hidden" id="novalnetInvalidReason" value="[{oxmultilang ident='NOVALNET_INVALID_CANCEL_REASON'}]">
                                                         <input type="hidden" id="novalnetCancelReason" value="[{oxmultilang ident='NOVALNET_CANCEL_REASON'}]">
                                                        <b>[{oxmultilang ident="NOVALNET_SUBSCRIPTION_CANCEL_LABEL" suffix="COLON" }]</b>
                                                        <select id="cancelReason" name="novalnet[sCancelReason]">
                                                            <option value=''>[{ oxmultilang ident="NOVALNET_PLEASE_SELECT" }]</option>
                                                            [{foreach from=$oView->getNovalnetSubsReasons() item=reason}]
                                                                <option value='[{$reason}]'>[{$reason}]</option>
                                                            [{/foreach}]
                                                        </select>
                                                        <button type="submit" class="submitButton">[{oxmultilang ident="NOVALNET_UPDATE" }]</button>
                                                    </form>
                                                    <br>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            [{/if}]
                            <tr>
                                <td colspan="2">
                                    <span id="accOrderNovalnetTransactionDetails_[{$order->oxorder__oxordernr->value}]">
                                        [{if $order->oxorder__novalnet_transaction->value != ''}]
                                            [{ oxmultilang ident="NOVALNET_TRANSACTION_DETAILS" }]<br>
                                            [{$order->oxorder__novalnet_transaction->value|html_entity_decode}]
                                        [{/if}]
                                        [{if $order->oxorder__novalnet_comments->value != ''}]
                                            [{$order->oxorder__novalnet_comments->value|html_entity_decode}]
                                        [{/if}]
                                        [{$order->oxorder__novalnetcomments->value|html_entity_decode}]
                                    </span>
                                </td>
                            </tr>
                        [{/if}]
                        <!-- Novalnet code ends -->
                    </table>
                </li>
            [{/foreach}]
        </ul>
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigation() place="bottom"}]
    [{else}]
        [{ oxmultilang ident="ORDER_EMPTY_HISTORY" }]
    [{/if}]
[{/if}]
