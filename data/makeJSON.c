
 
#include <stdio.h>
#include <stdlib.h>

/* allow GNU line reading to replace scanf */
#include <readline/readline.h>
#include <readline/history.h>

/* allow string copying and comparing */
#include <string.h>

/* get information to output detailed error messages */
#include <errno.h>

#define BUFFER 1024
#define TRUE 1
#define FALSE 0

int main ( void )
{
	/* Init */
	char * inFilename = "orgs.txt";
	char * outFilename = "orgList.json";
	
	FILE * input = fopen(inFilename, "r");
	if(input == NULL){
		fprintf(stderr,"Failed to open file: %s", inFilename);
		exit(-1);
	}
	
	FILE * output = fopen(outFilename, "w");
	if(output == NULL){
		fprintf(stderr,"Failed to open file: %s", inFilename);
		exit(-1);
	}
	
	/* parse and print */
	
	fprintf(output,"[\n");
	
	char s[BUFFER];
	char * done;
	char firstLoop = TRUE;
	for(;;){

		/* if no more lines, close JSON file */
		done = fgets(s, BUFFER, input);
		if(done == NULL){
			fprintf(output,"\t}\n");
			break;
		}
		
		/* if another object follows, close off previous one */
		if(firstLoop == TRUE)firstLoop = FALSE;
		else fprintf(output, "\t},\n");
		
		strtok(s, "\n");	
		fprintf(output, "\t{\n");
		fprintf(output, "\t\t\"SID\": ");
		fprintf(output, "\"%s\"\n", s);
	}
	
	fprintf(output,"]\n");
	fclose(input);
	fclose(output);
	return 0;
}



