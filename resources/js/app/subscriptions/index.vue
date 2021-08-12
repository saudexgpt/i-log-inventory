<style type="text/css">
	.payButton {
		background-color: #29b263;;
		border-color: #29b263;;
		padding: 10px;
		border-radius: 10px;
		color:  #fff;
		font-size: 20px;
		margin-top: 10px;
	}
</style>
<template>
  <div class="app-container">
    <span v-if="sub_details" class="pull-right">
      <paystack
        :amount="amount"
        :email="email"
        :paystackkey="paystackkey"
        :reference="reference"
        :callback="callback"
        :close="close"
        :embed="false"
      >
        <i class="fas fa-money-bill-alt" />
        Pay Due Subscription
      </paystack>
    </span>
    <v-client-table v-model="subscription_payments" :columns="columns" :options="options">

      <div
        slot="paid"
        slot-scope="props"
      >{{ currency + Number(props.row.paid).toLocaleString() }}</div>
      <div
        slot="amount_due"
        slot-scope="props"
      >{{ currency + Number(props.row.amount_due).toLocaleString() }}</div>
      <div
        slot="due_date"
        slot-scope="props"
      >{{ moment(props.row.due_date).format('MMMM D, YYYY') }}</div>
      <!-- <div slot="action" slot-scope="props">
        <a class="btn btn-default" @click="invoice=props.row; page.option='invoice_details'">
          <i class="el-icon-tickets" />
        </a>
      </div> -->
    </v-client-table>

  </div>
</template>

<script type="text/javascript">
import moment from 'moment';
import paystack from 'vue-paystack';
import Resource from '@/api/resource';
const subscriptionResource = new Resource('subscribe/pay-via-card');
export default {
  components: {
    paystack,
  },
  data(){
    return {
      paystackkey: process.env.MIX_PAYSTACK_API_KEY, // 'pk_test_a32396d591431a2c57e4c68a3cfa8fb15502a4b3', // paystack public key
      email: '', // Customer email
      amount: 0, // in kobo
      amount_due: 0,
      currency: '₦',
      payment_response: '',
      sub_details: '',
      subscription_payments: [],
      columns: [
        'amount_due',
        'paid',
        'transaction',
        'message',
        'status',
        // 'invoice_date',
        'due_date',
        // 'waybill_generated',
        // 'confirmed_by',
      ],

      options: {
        rowAttributesCallback: row => {
          if (row.status === 'pending') {
            var d = this.moment(new Date()).format('YYYY-MM-DD HH:mm:ss'); // today
            console.log(d);
            if (row.due_date > d) {
              return { 'style': 'background: #cccccc;' };
            } else {
              return { 'style': 'background: #ff3547;' };
            }
          } else {
            return { 'style': 'background: #77d46a;' };
          }
        },
        headings: {
          transaction: 'TXN NO.',
          paid: 'Amount Paid',
          // invoice_date: 'Date',
          due_date: 'Due Date',
        },
        pagination: {
          dropdown: true,
          chunk: 10,
        },
        perPage: 10,
        // editableColumns:['name', 'category.name', 'sku'],
        sortable: [
          'amount_due',
          'paid',
          'transaction',
          'due_date',
        ],
        // filterable: false,
        filterable: [
          'amount_due',
          'paid',
          'transaction',
          'due_date',
        ],
      },
    };
  },
  computed: {
    reference(){
      let text = '';
      const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

      for (let i = 0; i < 10; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
      }

      return text;
    },
  },
  created: function() {
    this.fetchSubscription();
  },
  methods: {
    moment,
    async fetchSubscription() {
      const subscription = await this.$store.dispatch('subscribe/fetchSubscription');
      const subscription_payment = subscription.subscription_payment;
      this.subscription_payments = subscription.subscription_payments;
      if (subscription_payment) {
        this.sub_details = subscription_payment;
        this.amount = subscription_payment.amount_due * 100; // in kobo
      }
      this.email = 'sammy4best@gmail.com';
    },
    callback: function(response){
      // console.log(response)
      this.payment_response = response;
      this.payViaCard();
    },
    close: function(){
      console.log('Payment closed');
    },

    payViaCard() {
      const app = this;
      const param = app.payment_response;
      const id = app.sub_details.id;
      const amount = app.amount / 100;
      param.payment_id = id;
      param.amount = amount;
      param._token = '';// put a token

      subscriptionResource.store(param).then(response => {
        console.log(response);
        // app.notifyMe('Payment made sucessfully', 'Success!!!', 'success');

        // app.$router.go('/order-details/' + id);
        app.$router.replace('/dashboard');
      });
    },
  },
};
</script>
