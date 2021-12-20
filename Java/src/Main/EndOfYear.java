package Main;

import java.io.IOException;

public class EndOfYear {

    public static void main(String[] args) throws IOException
    {
    	//Must have  "-flag <input-file> <output-path-or-file>"
        if( args.length == 3 ) {
        	String flag = args[0];
        	if (flag.equals("-e")) Extract.extractPDFtoJSON(args[1], args[2]);
        	else if (flag.equals("-c"));
        } else {
        	usage();
        }
    }
    
    /**
     * This will print out a message telling how to use this example.
     */
    private static void usage()
    {
        System.err.println( "usage: " + EndOfYear.class.getName() + " -e|f <input-file> <output-path-or-file>" );
    }

}
