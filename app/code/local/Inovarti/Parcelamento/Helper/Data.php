<?php

/**
 *
 * @category   Inovarti
 * @package    Inovarti_Parcelamento
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Parcelamento_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isEnable() {
        return Mage::getStoreConfig('payment/parcelamento/enabled');
    }

    public function isEnableDescontoVista() {
        return Mage::getStoreConfig('payment/parcelamento/desconto_parcelamento_enable');
    }
    public function MostraPrecoAvistaProduct($_product) {
        if ($this->isEnable()) {
            $html = '';
            $boleto = 0;

            $_coreHelper = Mage::helper('core');

            $_price = $_product->getPrice();
            $_finalPrice = $_product->getFinalPrice();


            $numero_de_parcelas = Mage::getStoreConfig('payment/parcelamento/parcelamento');
            $valor_parcelas = Mage::getStoreConfig('payment/parcelamento/price_min');
            $_desconto_parcelamento = Mage::getStoreConfig('payment/parcelamento/desconto_parcelamento');

            $valor = ($_finalPrice == $_price) ? $_price : $_finalPrice;

            if (($_desconto_parcelamento > 0) && ($valor > $valor_parcelas)) $boleto = $valor - ($valor * $_desconto_parcelamento / 100);

            if ($boleto > 0 && $this->isEnable()) {
                $boleto = number_format($boleto, 2, ',', '.');
                //number_format($boleto, 2, ',', '.')
                //$boleto_explode = explode(".", $boleto);
                $html .= '<span class="r">R$ </span><span class="price">' . $boleto . '</span>';
            }
            //if ($_desconto_parcelamento > 0) $html .= '<p class="discount">(' . $_desconto_parcelamento . '% de desconto)</p>';
        }

        return $html;
    }
    public function MostraFreteGratis($_product) {
        if ($this->isEnable()) {
            $html = '';
            $_finalPrice = $_product->getFinalPrice();
            if ($_finalPrice > 200 && $this->isEnable()) {
                $html .= '<span class="box-frete-gratis text-center"><i class="icon shipping-free"></i>Frete Grátis</span>';
            }
        }
        return $html;
    }
    public function MostraParcelaProduct($_product) {


        if ($this->isEnable()) {
            $html = '';
            $boleto = 0;

            $_coreHelper = Mage::helper('core');

            $_price = $_product->getPrice();
            $_finalPrice = $_product->getFinalPrice();


            $numero_de_parcelas = Mage::getStoreConfig('payment/parcelamento/parcelamento');
            $valor_parcelas = Mage::getStoreConfig('payment/parcelamento/price_min');
            $_desconto_parcelamento = Mage::getStoreConfig('payment/parcelamento/desconto_parcelamento');

            $valor = ($_finalPrice == $_price) ? $_price : $_finalPrice;

            if ($valor > $valor_parcelas) {
                for ($counter = $numero_de_parcelas; $counter >= 1; $counter--) {
                    if ($valor / $counter >= $valor_parcelas) {
                        $valor_parcelas = $valor / $counter;
                        $numero_de_parcelas = $counter;
                        break;
                    }
                }
                if ($valor == $valor_parcelas)
                    $valor_parcelas = ($_finalPrice == $_price) ? number_format($_price, 2) : number_format($_finalPrice, 2);
            }else {
                $valor_parcelas = ($_finalPrice == $_price) ? $_price : $_finalPrice;
                $numero_de_parcelas = 1;
            }

            $final_valor_parcelas = $_coreHelper->currency($valor_parcelas, true, false);

            if (($_desconto_parcelamento > 0) && ($valor > $valor_parcelas))
                $boleto = $valor - ($valor * $_desconto_parcelamento / 100);

            $html .= '<p class="plots">';

            if ($_finalPrice > $_price):
            //$html .= '(economia de ' . $_coreHelper->currency($_finalPrice - $_price, true, true) . ')';
            endif;

            if ($numero_de_parcelas > 1):
                $html .= 'em ' . $numero_de_parcelas . 'x de <span>' . $final_valor_parcelas . '</span> sem juros';
            endif;
            $html .= '</p>';


            if ($boleto > 0 && $this->isEnable()) {
                $boleto = number_format($boleto, 2, ',', '.');
                //number_format($boleto, 2, ',', '.')
                //$boleto_explode = explode(".", $boleto);
                $html .= '<p class="avista"><span class="line"><span class="ou">ou</span></span> <span class="r">R$ </span><span class="price">' . $boleto . '</span><span> à vista no boleto ou cartão</span></p> ';
            }
            if ($_desconto_parcelamento > 0 && $boleto > 0)
                $html .= '<p class="discount">(' . $_desconto_parcelamento . '% de desconto)</p>';
        }

        return $html;
    }

    public function MostraParcelaProductdetalhes($_product) {

        if ($this->isEnable()) {
            $html = '<div class="col-md-7">';

            $numero_de_parcelas = Mage::getStoreConfig('payment/parcelamento/parcelamento');
            $valor_parcelas = Mage::getStoreConfig('payment/parcelamento/price_min');
            $_desconto_parcelamento = Mage::getStoreConfig('payment/parcelamento/desconto_parcelamento');

            $_price = $_product->getPrice();
            $_finalPrice = $_product->getFinalPrice();
            $descontoporcentagem = 0;
            $valor = ($_finalPrice == $_price) ? $_price : $_finalPrice;

            if ($valor > $valor_parcelas) {
                for ($counter = $numero_de_parcelas; $counter >= 1; $counter--) {
                    if ($valor / $counter >= $valor_parcelas) {
                        $numero_de_parcelas = $counter;
                        break;
                    }
                }
            } else {
                $numero_de_parcelas = 1;
            }

            $divmeio = $numero_de_parcelas / 2;

            //for ($i = $numero_de_parcelas; $i >= 1; $i--) {
            for ($i = 1; $i <= $numero_de_parcelas; $i++) {
                $installmentValue = round($valor / $i, 2);
                if ($i == 1) {
                    if (($_desconto_parcelamento > 0) && ($valor > $valor_parcelas)){
                        $descontoporcentagem = $valor - ($valor * $_desconto_parcelamento / 100);
                        $html .= "<span><strong>&#192; vista </strong><span class='cor'>" . Mage::helper('core')->currency(($descontoporcentagem), true, false) . "</span> (" . $_desconto_parcelamento . "% de desconto)</span>";
                    }
                } else {
                    $html .= "<span><strong>" . $i . "x</strong> sem juros <span class='cor'>" . Mage::helper('core')->currency(($installmentValue), true, false) . "</span></span>";
                }

                if ($divmeio == $i) {
                    $html .="</div><div class=\"col-md-5\">";
                }

                if ($installmentValue < $valor_parcelas) {
                    break;
                }
            }

            // caso o valor da parcela minima seja maior do que o valor da compra,
            // deixa somente opcao a vista
            if ($valor_parcelas > $valor) {
                $html .= "<span>&#192; vista " . Mage::helper('core')->currency(($valor), true, false) . "</span><br>";
            }


            $html .= "</div>";
            return $html;
        }
    }

    public function MostraParcelaTotal($_grandtotal) {
        if ($this->isEnable()) {
            $html = '';
            $boleto = 0;

            $_coreHelper = Mage::helper('core');

            $numero_de_parcelas = Mage::getStoreConfig('payment/parcelamento/parcelamento');
            $valor_parcelas = Mage::getStoreConfig('payment/parcelamento/price_min');
            $_desconto_parcelamento = Mage::getStoreConfig('payment/parcelamento/desconto_parcelamento');

            $valor = $_grandtotal;

            if ($valor > $valor_parcelas) {
                for ($counter = $numero_de_parcelas; $counter >= 1; $counter--) {
                    if ($valor / $counter >= $valor_parcelas) {
                        $valor_parcelas = $valor / $counter;
                        $numero_de_parcelas = $counter;
                        break;
                    }
                }
                if ($valor == $valor_parcelas)
                    $valor_parcelas = number_format($valor, 2);
            }else {
                $valor_parcelas = $valor;
                $numero_de_parcelas = 1;
            }

            $final_valor_parcelas = $_coreHelper->currency($valor_parcelas, true, false);

            if (($_desconto_parcelamento > 0) && ($valor > $valor_parcelas))
                $boleto = $valor - ($valor * $_desconto_parcelamento / 100);

            $html .= '<p class="plots">';

            if ($numero_de_parcelas > 1):
                $html .= 'em ' . $numero_de_parcelas . 'x de <span>' . $final_valor_parcelas . '</span> sem juros';
            endif;
            $html .= '</p>';

            if ($boleto > 0 && $this->isEnable()) {
                $boleto = number_format($boleto, 2, ',', '.');
                //number_format($boleto, 2, ',', '.')
                //$boleto_explode = explode(".", $boleto);
                $html .= '<p class="avista"><span class="line"><span class="ou">ou</span></span> <span class="r">R$ </span><span class="price">' . $boleto . '</span><span> à vista no boleto ou cartão</span></p> ';
            }
        }

        return $html;
    }

}