package Main;

import java.io.File;
import java.io.IOException;
import java.io.StringWriter;
import java.io.Writer;
import java.lang.reflect.Array;
import java.util.ArrayList;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;

public class ExtractTransactions {
	
	public class Transaction {
		
		public enum TransactionType {
			Purchase,
			OnlineTransfer,
			Recurring,
			EDeposit,
			MobileDeposit,
			Check,
		}
		
		public int Month;
		public int Day;
		public String Title;
		public TransactionType type;
		
		public static boolean isTransactionStart(String line) {
			if (line.trim().length() > 0) {
				String firstWord = line.split(" ")[0];
				return firstWord.matches("^([0-9]+\\/[0-9]+)");
			}
			return false;
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
			
			return value;
		}
		public static boolean isTransactionEnd(String line) {
			String value = Transaction.getTransactionValue(line);
			return (value.length() > 0);
		}
	}
	
	private static String extractTextFromPDF(String pathToPDF) throws IOException {
		PDDocument doc = null;
		String text = new String();
        try
        {
            PDDocument document = PDDocument.load(new File(pathToPDF));
            Writer sw = new StringWriter();
            PDFTextStripper stripper = new PDFTextStripper();
            stripper.setStartPage(1);
            stripper.setEndPage(document.getNumberOfPages());
            stripper.writeText( document,  sw );
            text = sw.toString();
            
        } finally {
            if( doc != null )
            {
                doc.close();
            }
        }
        
        return text;
	}

	public static ArrayList<Transaction> extractTractionsFromStatement(String pathToPDF) throws IOException {
		//The entire PDF as a string
		String pdftext = ExtractTransactions.extractTextFromPDF(pathToPDF);
		//The entire PDF as an array of strings, split on new lines
		String[] lines = pdftext.split("[\\r\\n]+");
		
		ArrayList<Transaction> transactions = new ArrayList<Transaction>();
		
		//Where the transaction lines begin and end
		String startWords = "Transaction history";
		boolean foundStart = false;
		String endWords = "Ending balance on";
		
		//Line index
		int lineNumber = 0;
		//Keep track of if the start of the transaction has been found.
		boolean foundTransactionStart = false;
		StringBuilder transactionString = new StringBuilder();
		//Skip everything up to the start of the transactions
		while(!lines[lineNumber++].equals(startWords));
		//Then iterate over transaction data
		while(lineNumber < lines.length) {
			//TODO: Improve this
			if (lines[lineNumber].contains(endWords)) break;
			else if (lines[lineNumber].trim().length() == 0) lineNumber++;
			//Otherwise try to parse out the line, given the current state.
			else {
				if (!foundTransactionStart) {
					//Looking for a date at the start of the line
					if (Transaction.isTransactionStart(lines[lineNumber])) {
						foundTransactionStart = true;
						transactionString.append(lines[lineNumber]);
					}
				}
				
				if (foundTransactionStart) {
					if (!transactionString.toString().equals(lines[lineNumber]))
						transactionString.append(" " + lines[lineNumber]);
					
					if (Transaction.isTransactionEnd(lines[lineNumber])) {
						foundTransactionStart = false;
						
						String transaction  = transactionString.toString();
						System.out.println(transaction);
						transactionString = new StringBuilder();
					}
				}
				
				lineNumber++;
			}
		} 
		

		
		return transactions;
		
	}
}
