package Main;

import java.io.IOException;

public class EndOfYear {

    public static void main(String[] args) throws IOException
    {
        if( args.length != 1 )
        {
            usage();
        }
        else
        {
        	ExtractTransactions.extractTractionsFromStatement(args[0]); 
        }
    }
    
    /**
     * This will print out a message telling how to use this example.
     */
    private static void usage()
    {
        System.err.println( "usage: " + EndOfYear.class.getName() + " <input-file>" );
    }

}