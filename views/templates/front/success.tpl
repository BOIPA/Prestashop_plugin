{*
 * @author    BOIPA
 * @copyright Copyright (c) 2018 BOIPA
 * @license   http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 *
*}
{capture name=path}{l s='Proceed with Payment' mod='boipa'}{/capture}

<div class="box clearfix">
    <h2 style="margin: 30px 0">
        {l s='Order status' mod='boipa'} {$orderPublicId} -
        {if $orderEvoStatus!='success'}
            <span style="color: red; font-weight: bold;">{$orderStatus}</span>
        {else}
            {$orderStatus}
        {/if} <br/>
    </h2>

    {$HOOK_ORDER_CONFIRMATION}
    {$HOOK_PAYMENT_RETURN}

    {if $orderEvoStatus!='success'}
        <a class="button btn btn-default button-medium pull-right"
           href="{$link->getModuleLink('boipa', 'payment', ['retry' => $orderId, 'retryToken' => $token], true)|escape:'html'}">
            <span>
                {l s='Try to pay again' mod='boipa'}
                <i class="icon-chevron-right"></i>
            </span>
        </a>
    {/if}
    <a class="button btn btn-default button-medium pull-left" href="{$redirectUrl}">
        <span>
            {l s='Order details' mod='boipa'}
            <i class="icon-chevron-right"></i>
        </span>
    </a>
</div>