(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	async function triggerTransferPolygonNFTs(options) {
		console.log('triggerTransferPolygonNFTs', options);

		// hide modal form
		$('#transferForm').hide();

		// show loader
		$('#response').html('<p>Confirming...</p>').show();

		try {
			const transaction = await Moralis.transfer(options);
			// console.log('transaction', transaction);

			// show tx hash link
			$('#response').html('<a target="_blank" href="https://polygonscan.com/tx/' + transaction.hash + '">View on polygonscan</a>').show();

			const result = await transaction.wait();
			console.log('result', result);

			$('#response').html('<a target="_blank" href="https://polygonscan.com/tx/' + transaction.hash + '">Trasaction completed</a>').show();

		} catch (error) {
			console.log(error);

			$('#response').html('<p>Error, please try again.</p>').show();

		}
	}

	$(function () {

		// TODO: track if user logged in
		loginWithMetaMask();

		const modal = $('#transferModal');
		const closeBtn = $(".modal-close");

		closeBtn.on('click', function () {
			modal.hide()

			// reset form
			$('#transferForm').show();
			$('#response').html('').hide();
			$("#transferToAdress").val();
			$("#contractAddress").val();
			$("#tokenId").val();
		});

		// ? fix
		// window.onclick = function(event) {
		//     if (event.target !== modal) {
		//         modal.style.display = "none";
		//         console.log('modal closed');
		//     }
		// }

		$('#transferForm').on('submit', function (e) {
			e.preventDefault(); //prevent form from submitting
		});

		// TODO: implement
		function isAdressValid(adress) {
			return true;
		}

		$('#submitTransfer').on('click', function (e) {

			const receiver = $("#transferToAdress").val();
			const contractAddress = $("#contractAddress").val();
			const tokenId = $("#tokenId").val();

			if (!receiver || !isAdressValid(receiver)) {
				alert('Please enter a valid address');
				return;
			}

			const options = {
				// type: "erc1155",
				type: "erc721",
				receiver: receiver,
				contractAddress: contractAddress,
				tokenId: tokenId,
				amount: 1
			};

			// trigger web3 transfer flow
			triggerTransferPolygonNFTs(options);
		});

		$('.btn-transfer-nft').on('click', function (e) {
			e.stopImmediatePropagation();
			e.preventDefault();

			// update hidden form fields values
			$("#contractAddress").val($(this).data('nft-contract-adress'));
			$("#tokenId").val($(this).data('nft-token-id'));

			// show modal
			modal.show();	
		});
	});

})(jQuery);
