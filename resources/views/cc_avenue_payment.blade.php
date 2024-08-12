<style>
  div#pageloader12 {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-content: center;
    justify-content: center;
    align-items: center;
    height: 70vh;
}
</style>

<section class="cc-payment-cus1">
  <div class="container">
    <div class="row justify-content-center">
          <div class="col-md-12 col-lg-10 hove11 make-payment1 make-payment-cus1">
              
              <div class="row text-center  pt-3 " style="margin: auto;">
                <div class="col-md-12">
                  @php
                   
                   $url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
                   
                  @endphp
               
                <form method="post" name="redirect" action="{{ $url ?? ''}}"> 
                    @php
                    $token = csrf_token();
                        echo "<input type=hidden name=encRequest value=$encrypted_data>";
                        echo "<input type=hidden name=access_code value=$access_code>";
                        echo "<input type=hidden name=_token value=$token>";
                    @endphp
                    
                  </form>
                  <script language='javascript'>document.redirect.submit();</script>
                </div>
              </div>
          </div>
         
    </div>
  </div>
</section>