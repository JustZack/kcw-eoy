package Main;

import java.io.File;
import java.io.FileWriter;
import java.util.*;
import java.io.IOException;
import java.io.StringWriter;
import java.io.Writer;
import java.lang.reflect.Array;
import java.util.ArrayList;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;

public class Extract {
	
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
	
	public static ArrayList<Transaction> extractTractionsFromLines(String[] lines) throws IOException {		
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
						transactions.add(Transaction.buildFromString(transaction));
						transactionString = new StringBuilder();
					}
				}
				
				lineNumber++;
			}
		} 
		
		return transactions;
		
	}

	public static String extractFirstDate(String[] lines) throws IOException {
		ArrayList<String> months = new ArrayList<String>(Arrays.asList("january", "febuary", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"));
		for (String line : lines) {
			if (line.trim().length() > 0) {
				String[] parts = line.toLowerCase().split(" ");
				if (months.contains(parts[0])) {
					String date = String.format("%s %s%s", parts[0], parts[1], parts[2]);
					return date.replace(',', ' ').replace(' ', '-');
				}
			}
		}
		
		return new String();
	}
	
	public static void extractPDFtoJSON(String pathToPDF) throws IOException {
		//The entire PDF as a string
		String pdftext = Extract.extractTextFromPDF(pathToPDF);
		//The entire PDF as an array of strings, split on new lines
		String[] lines = pdftext.split("[\\r\\n]+");
		
		ArrayList<Transaction> transactions = Extract.extractTractionsFromLines(lines);
		String statementDate = Extract.extractFirstDate(lines);
		
		StringBuilder JSON = new StringBuilder();
		JSON.append("[\n");
		for (int i = 0;i < transactions.size();i++) {
			String toAppend = transactions.get(i).toJSON();
			if (i < transactions.size() - 1) toAppend += ",";
			JSON.append(toAppend+"\n");
		}
		JSON.append("]");
		
		FileWriter file = new FileWriter(statementDate+".json");
		file.write(JSON.toString());
		file.close();
	}
}
