package Main;

public class Transaction {
	
	public enum TransactionType {
		Purchase,
		OnlineTransferTo,
		OnlineTransferFrom,
		RecurringTransferTo,
		RecurringPayment,
		Deposit,
		PaymentFromPaypal,
		PaypalPayment,
		Check,
		Other
	}
	
	public String Month;
	public String Day;
	public String Memo;
	public String Value;
	public TransactionType type;
	public String transactionString;
	
	public Transaction() {
		
	}
	
	public String toString() {
		return "[" + this.type.name() + "] " + this.Value + " " + this.Month + "/" + this.Day + " " + this.Memo;
	}
	
	public static String getTransactionDate(String line) {
		if (line.trim().length() > 0) {
			String firstWord = line.split(" ")[0];
			if (firstWord.matches("^([0-9]+\\/[0-9]+)")) {
				return firstWord;
			}
		}
		return new String("");
	} 
	public static boolean isTransactionStart(String line) {
		String value = Transaction.getTransactionDate(line);
		return (value.length() > 0);
	}
	
	private static String transactionValueRegex = "(([0-9]?[0-9]?[0-9],)?[0-9]?[0-9]?[0-9]\\.[0-9]{2})";
	public static String getTransactionValue(String line) {
		String[] words = line.split(" ");
		String value = new String("");
		if (words.length > 1) {
			String[] lastWords = new String[2];
			lastWords[0] = words[words.length-2];
			lastWords[1] = words[words.length-1];
			
			if (lastWords[0].matches(transactionValueRegex)) value = lastWords[0];
			else if (lastWords[1].matches(transactionValueRegex)) value = lastWords[1];
		} else if (words.length > 0) {
			if (words[0].matches(transactionValueRegex)) value = words[0];
		}
		
		return value.trim();
	}
	public static boolean isTransactionEnd(String line) {
		String value = Transaction.getTransactionValue(line);
		return (value.length() > 0);
	}
	
	public static TransactionType getTransactionType(String memo) {
		memo = memo.toLowerCase();
		
		if (memo.contains("purchase"))
			return TransactionType.Purchase;
		else if (memo.contains("online transfer to"))
			return TransactionType.OnlineTransferTo;
		else if (memo.contains("online transfer from"))
			return TransactionType.OnlineTransferFrom;
		else if (memo.contains("recurring transfer to"))
			return TransactionType.RecurringTransferTo;
		else if (memo.contains("recurring payment"))
			return TransactionType.RecurringPayment;
		else if (memo.contains("check"))
			return TransactionType.Check;
		else if (memo.contains("edeposit in") || memo.contains("deposit"))
			return TransactionType.Deposit;
		else if (memo.contains("rtp from paypal"))
			return TransactionType.PaymentFromPaypal;
		else if (memo.contains("paypal inst xfer"))
			return TransactionType.PaypalPayment;
		
		
		return TransactionType.Other;
	}
	
	public static Transaction buildFromString(String transactionStr) {
		Transaction t = new Transaction();
		t.transactionString = transactionStr;
		String[] dateParts = Transaction.getTransactionDate(transactionStr).split("\\/");
		t.Month = dateParts[0];
		t.Day = dateParts[1];
		t.Value = Transaction.getTransactionValue(transactionStr);
		
		String memo = transactionStr.substring(transactionStr.indexOf(" "));
		t.Memo = memo.substring(0, memo.indexOf(t.Value)).trim();
		t.type = Transaction.getTransactionType(t.Memo);
		
		System.out.println(t.toString());
		return t;
	}
}
