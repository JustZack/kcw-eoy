package Main;

import java.io.IOException;

public class EndOfYear {

    public static void main(String[] args) throws IOException
    {
        if( args.length != 2 )
        {
            usage();
        }
        else
        {
        	Extract.extractPDFtoJSON(args[0], args[1]); 
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
